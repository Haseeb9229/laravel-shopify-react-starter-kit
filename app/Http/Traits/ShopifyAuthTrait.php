<?php

namespace App\Http\Traits;

use App\Models\User;
use Osiset\ShopifyApp\Util;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Illuminate\Http\RedirectResponse;
use App\Providers\RouteServiceProvider;
use Illuminate\Contracts\View\View as ViewView;
use Osiset\ShopifyApp\Actions\AuthenticateShop;
use Osiset\ShopifyApp\Objects\Values\ShopDomain;
use Osiset\ShopifyApp\Exceptions\MissingAuthUrlException;
use Osiset\ShopifyApp\Exceptions\MissingShopDomainException;
use Osiset\ShopifyApp\Exceptions\SignatureVerificationException;

/**
 * Responsible for authenticating the shop.
 */
trait ShopifyAuthTrait
{
    /**
     * Installing/authenticating a shop.
     *
     * @throws MissingShopDomainException if both shop parameter and authenticated user are missing
     *
     * @return ViewView|RedirectResponse
     */
    public function authenticate(Request $request, AuthenticateShop $authShop)
    {
        if ($request->missing('shop') && !$request->user()) {
            // One or the other is required to authenticate a shop
            throw new MissingShopDomainException('No authenticated user or shop domain');
        }

        // Get the shop domain
        $shopDomain = $request->has('shop')
        ? ShopDomain::fromNative($request->get('shop'))
        : $request->user()->getDomain();

        // If the domain is obtained from $request->user()
        if ($request->missing('shop')) {
            $request['shop'] = $shopDomain->toNative();
        }
        // Run the action
        [$result, $status] = $authShop($request);

        if ($status === null) {
            // Show exception, something is wrong
            throw new SignatureVerificationException('Invalid HMAC verification');
        } elseif ($status === false) {
            if (!$result['url']) {
                throw new MissingAuthUrlException('Missing auth url');
            }

            $shopDomain = $shopDomain->toNative();
            $shopOrigin = $shopDomain ?? $request->user()->name;

            return View::make(
                'shopify-app::auth.fullpage_redirect',
                [
                    'apiKey' => Util::getShopifyConfig('api_key', $shopOrigin),
                    'appBridgeVersion' => Util::getShopifyConfig('appbridge_version') ? '@' . config('shopify-app.appbridge_version') : '',
                    'authUrl' => $result['url'],
                    'host' => $request->get('host'),
                    'shopDomain' => $shopDomain,
                ]
            );
        } else {
            $user_id = $result['shop_id']->toNative();
            if($request->has('hmac') && $request->has('host')){
                $user = User::find($user_id);
                // $user->update([
                //     'hmac' => $request->get('hmac'),
                //     'host' => $request->get('host'),
                //     // 'plan_id' => !$user->plan_id ? 1 : $user->plan_id
                // ]);
            }
            auth()->loginUsingId($user_id);
            return redirect()->intended(RouteServiceProvider::$home);

        }
    }

    /**
     * Get session token for a shop.
     *
     * @return ViewView
     */
    public function token(Request $request)
    {
        $request->session()->reflash();
        $shopDomain = ShopDomain::fromRequest($request);
        $target = $request->query('target');
        $query = parse_url($target, PHP_URL_QUERY);

        $cleanTarget = $target;
        if ($query) {
            // remove "token" from the target's query string
            $params = Util::parseQueryString($query);
            $params['shop'] = $params['shop'] ?? $shopDomain->toNative() ?? '';
            $params['host'] = $request->get('host');
            unset($params['token']);

            $cleanTarget = trim(explode('?', $target)[0] . '?' . http_build_query($params), '?');
        } else {
            $params = ['shop' => $shopDomain->toNative() ?? '', 'host' => $request->get('host')];
            $cleanTarget = trim(explode('?', $target)[0] . '?' . http_build_query($params), '?');
        }
        $user = User::firstWhere('name', $shopDomain->toNative());
        auth()->login($user);
        return redirect()->intended(RouteServiceProvider::$home);
    }
}
