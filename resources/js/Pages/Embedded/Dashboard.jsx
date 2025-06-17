import {
    Box, Button, Card, ChoiceList,
    IndexFilters,
    IndexTable,
    InlineStack,
    Page, RangeSlider, Text, TextField, Popover, ActionList,
    useBreakpoints, useIndexResourceState, useSetIndexFiltersMode
} from '@shopify/polaris';
import { useCallback, useEffect, useState } from 'react';
import { usePage } from '@inertiajs/react';
import { DeleteIcon, EditIcon } from '@shopify/polaris-icons';



function FiancialFilterDropdown({ onFiancialStatusChange }) {
    const [popoverActive, setPopoverActive] = useState(false);

    const togglePopoverActive = useCallback(() => {
        setPopoverActive((popoverActive) => !popoverActive);
    }, []);

    const handleItemClick = (status) => {
        onFiancialStatusChange(status);
        setPopoverActive(false);
    };

    const activator = (
        <Button onClick={togglePopoverActive} disclosure>
            Status Filter
        </Button>
    );

    return (
        <div style={{ display: 'flex', justifyContent: 'flex-end', height: '50px' }}>
            <Popover
                active={popoverActive}
                activator={activator}
                autofocusTarget="first-node"
                onClose={togglePopoverActive}
            >
                <ActionList
                    actionRole="menuitem"
                    items={[
                        { content: 'paid', onAction: () => handleItemClick('paid') },
                        { content: 'unpaid', onAction: () => handleItemClick('unpaid') },
                        { content: 'partially paid', onAction: () => handleItemClick('partially paid') },
                    ]}
                />
            </Popover>
        </div>
    );
}
function FulfilledFilterDropdown({ onFulfillStatusChange }) {
    const [popoverActive, setPopoverActive] = useState(false);

    const togglePopoverActive = useCallback(() => {
        setPopoverActive((popoverActive) => !popoverActive);
    }, []);

    const handleItemClick = (status) => {
        onFulfillStatusChange(status);
        setPopoverActive(false);
    };

    const activator = (
        <Button onClick={togglePopoverActive} disclosure>
            Fulfill Status Filter
        </Button>
    );

    return (
        <div style={{ display: 'flex', justifyContent: 'flex-end', height: '50px', marginRight: '10px' }}>
            <Popover
                active={popoverActive}
                activator={activator}
                autofocusTarget="first-node"
                onClose={togglePopoverActive}
            >
                <ActionList
                    actionRole="menuitem"
                    items={[
                        { content: 'fulfilled', onAction: () => handleItemClick('fulfilled') },
                        { content: 'unfulfilled', onAction: () => handleItemClick('unfulfilled') },
                        { content: 'partially fulfilled', onAction: () => handleItemClick('partially fulfilled') },
                    ]}
                />
            </Popover>
        </div>
    );
}


export default function Dashboard() {
    const [reload, setReload] = useState(true);

    const [onFiancialStatusChange, setonFiancialStatusChange] = useState('');

    const [onFulfillStatusChange, setonFulfillStatusChange] = useState('');

    const [queryValue, setQueryValue] = useState('');
    
    const [syncOrders, setSyncOrders] = useState(false);


    const { query } = usePage().props.ziggy;



    const sleep = (ms) =>
        new Promise((resolve) => setTimeout(resolve, ms));



    const [itemStrings, setItemStrings] = useState([
        // 'All',
        // 'Unpaid',
        // 'Open',
        // 'Closed',
        // 'Local delivery',
        // 'Local pickup',
    ]);
    const deleteView = (index) => {
        const newItemStrings = [...itemStrings];
        newItemStrings.splice(index, 1);
        setItemStrings(newItemStrings);
        setSelected(0);
    };
    const duplicateView = async (name) => {
        setItemStrings([...itemStrings, name]);
        setSelected(itemStrings.length);
        await sleep(1);
        return true;
    };
    const tabs = itemStrings.map((item, index) => ({
        content: item,
        index,
        onAction: () => { },
        id: `${item}-${index}`,
        isLocked: index === 0,
        customerEmail:
            index === 0
                ? []
                : [
                    // {
                    //   type: 'rename',
                    //   onAction: () => {},
                    //   onPrimaryAction: async (value) => {
                    //     const newItemsStrings = tabs.map((item, idx) => {
                    //       if (idx === index) {
                    //         return value;
                    //       }
                    //       return item.content;
                    //     });
                    //     await sleep(1);
                    //     setItemStrings(newItemsStrings);
                    //     return true;
                    //   },
                    // },
                    // {
                    //   type: 'duplicate',
                    //   onPrimaryAction: async (value) => {
                    //     await sleep(1);
                    //     duplicateView(value);
                    //     return true;
                    //   },
                    // },
                    // {
                    //   type: 'edit',
                    // },
                    // {
                    //   type: 'delete',
                    //   onPrimaryAction: async () => {
                    //     await sleep(1);
                    //     deleteView(index);
                    //     return true;
                    //   },
                    // },
                ],
    }));
    const [selected, setSelected] = useState(0);
    const onCreateNewView = async (value) => {
        await sleep(500);
        setItemStrings([...itemStrings, value]);
        setSelected(itemStrings.length);
        return true;
    };
    const sortOptions = [
        { label: 'Order', value: 'order asc', directionLabel: 'Ascending' },
        { label: 'Order', value: 'order desc', directionLabel: 'Descending' },
        { label: 'Customer', value: 'customer asc', directionLabel: 'A-Z' },
        { label: 'Customer', value: 'customer desc', directionLabel: 'Z-A' },
        { label: 'Date', value: 'date asc', directionLabel: 'A-Z' },
        { label: 'Date', value: 'date desc', directionLabel: 'Z-A' },
        { label: 'Total', value: 'total asc', directionLabel: 'Ascending' },
        { label: 'Total', value: 'total desc', directionLabel: 'Descending' },
    ];
    const [sortSelected, setSortSelected] = useState(['order asc']);
    const { mode, setMode } = useSetIndexFiltersMode();
    const onHandleCancel = () => {
        setQueryValue('');
        setonFulfillStatusChange('');
        setonFiancialStatusChange('');
        setReload(true);

    };
    const handleSyncOrders = () => {
        setSyncOrders(true)
        setReload(true)
    }

    const onHandleSave = async () => {
        await sleep(1);
        return true;
    };

    const primaryAction =
        selected === 0
            ? {
                type: 'save-as',
                onAction: onCreateNewView,
                disabled: false,
                loading: false,
            }
            : {
                type: 'save',
                onAction: onHandleSave,
                disabled: false,
                loading: false,
            };
    const [accountStatus, setAccountStatus] = useState(
        undefined,
    );
    const [moneySpent, setMoneySpent] = useState(
        undefined,
    );
    const [taggedWith, setTaggedWith] = useState('');

    const handleTaggedWithRemove = useCallback(() => setTaggedWith(''), []);
    const handleQueryValueRemove = useCallback(() => setQueryValue(''), []);

    const handleAccountStatusChange = useCallback(
        (value) => setAccountStatus(value),
        [],
    );
    const handleMoneySpentChange = useCallback(
        (value) => setMoneySpent(value),
        [],
    );
    const handleTaggedWithChange = useCallback(
        (value) => setTaggedWith(value),
        [],
    );
    const handleAccountStatusRemove = useCallback(
        () => setAccountStatus(undefined),
        [],
    );
    const handleMoneySpentRemove = useCallback(
        () => setMoneySpent(undefined),
        [],
    );
    const handleFiltersClearAll = useCallback(() => {
        handleAccountStatusRemove();
        handleMoneySpentRemove();
        handleTaggedWithRemove();
        handleQueryValueRemove();
    }, [
        handleAccountStatusRemove,
        handleMoneySpentRemove,
        handleQueryValueRemove,
        handleTaggedWithRemove,
    ]);

    const filters = [
        {
            key: 'accountStatus',
            label: 'Account status',
            filter: (
                <ChoiceList
                    title="Account status"
                    titleHidden
                    choices={[
                        { label: 'Enabled', value: 'enabled' },
                        { label: 'Not invited', value: 'not invited' },
                        { label: 'Invited', value: 'invited' },
                        { label: 'Declined', value: 'declined' },
                    ]}
                    selected={accountStatus || []}
                    onChange={handleAccountStatusChange}
                    allowMultiple
                />
            ),
            shortcut: true,
        },
        {
            key: 'taggedWith',
            label: 'Tagged with',
            filter: (
                <TextField
                    label="Tagged with"
                    value={taggedWith}
                    onChange={handleTaggedWithChange}
                    autoComplete="off"
                    labelHidden
                />
            ),
            shortcut: true,
        },
        {
            key: 'moneySpent',
            label: 'Money spent',
            filter: (
                <RangeSlider
                    label="Money spent is between"
                    labelHidden
                    value={moneySpent || [0, 500]}
                    prefix="$"
                    output
                    min={0}
                    max={2000}
                    step={1}
                    onChange={handleMoneySpentChange}
                />
            ),
        },
    ];
    const appliedFilters = [];
    if (accountStatus && !isEmpty(accountStatus)) {
        const key = 'accountStatus';
        appliedFilters.push({
            key,
            label: disambiguateLabel(key, accountStatus),
            onRemove: handleAccountStatusRemove,
        });
    }
    if (moneySpent) {
        const key = 'moneySpent';
        appliedFilters.push({
            key,
            label: disambiguateLabel(key, moneySpent),
            onRemove: handleMoneySpentRemove,
        });
    }
    if (!isEmpty(taggedWith)) {
        const key = 'taggedWith';
        appliedFilters.push({
            key,
            label: disambiguateLabel(key, taggedWith),
            onRemove: handleTaggedWithRemove,
        });
    }

    const [logs, setLogs] = useState([
        // {
        //     id: '1',
        //     name: '23',
        //     status: 'campaign No 01',
        //     fullfilment_status: 'Deal of the Day',
        //     total_price: 'Fixed',

        // },
        // {
        //     id: '2',
        //     name: '23',
        //     status: 'campaign No 02',
        //     fullfilment_status: 'Home Page',
        //     total_price: 'random',


        // },
        // {
        //     id: '3',
        //     name: '23',
        //     status: 'campaign No 03',
        //     fullfilment_status: 'Test Data',
        //     total_price: 'Fixed',

        // },
    ]);
    const fetchData = async () => {
        try {
            const response = await fetch(route('search', { query: queryValue, ...query, financial_status: onFiancialStatusChange, fulfillment_status: onFulfillStatusChange  , sync_orders: syncOrders }));
            const result = await response.json();
            handleData(result)
        } catch (err) {
            console.error("API Failed =>", err);
        }
    };

    useEffect(() => {
        if (reload) {
            fetchData();
        }
    }, [reload]);

    useEffect(() => {
        setReload(true)
    }, [queryValue]);

    useEffect(() => {
        setReload(true)
    }, [onFiancialStatusChange]);

    useEffect(() => {
        setReload(true)
    }, [onFulfillStatusChange])
    



    const handleData = (data) => {
        console.log("this comes from Handle Data Function => ", data)
        const newLogs =
            (Array.isArray(data) ? data : []).map(orderItem => {
                const lineItems = Array.isArray(orderItem.line_item_order)
                    ? orderItem.line_item_order.map(item =>
                        `${item.quantity}`
                    ).join(', ')
                    : "No items";

                const orderName = Array.isArray(orderItem.line_item_order)
                    ? orderItem.line_item_order.map(item =>
                        `${item.title}`
                    ).join(', ')
                    : "No name";

                return {
                    id: orderItem.id || "No ID",
                    customer_name: orderItem.customer_order?.first_name + " " + orderItem.customer_order?.last_name || "Unknown",
                    order_name: orderName,
                    order_no: orderItem.name || "N/A",
                    quantity: lineItems,
                    status: orderItem.financial_status || "unpaid",
                    fullfilment_status: orderItem.fulfillment_status || "unfulfilled",
                    total_price: orderItem.total_price || 0,
                    country: orderItem.shipping_address_order?.country || "N/A",
                };
            })
        setLogs(newLogs);
        setReload(false);
    };

    const resourceName = {
        singular: 'order',
        plural: 'Orders',
    };

    const { selectedResources, allResourcesSelected, handleSelectionChange } =
        useIndexResourceState(logs);

    const rowMarkup = logs.map(
        (
            { id, order_no, customer_name, order_name, quantity, status, fullfilment_status, total_price, country },
            index,
        ) => (
            <IndexTable.Row
                id={id}
                key={id}
                selected={selectedResources.includes(id)}
                position={index}
            >
                <IndexTable.Cell>
                    <Text variant="bodyMd" fontWeight="bold" as="span">
                        {id}
                    </Text>
                </IndexTable.Cell>
                <IndexTable.Cell><Box paddingBlock={'500'}>{order_no}</Box> </IndexTable.Cell>
                <IndexTable.Cell><Box paddingBlock={'500'}>{customer_name}</Box></IndexTable.Cell>
                <IndexTable.Cell><Box paddingBlock={'500'}>{order_name}</Box></IndexTable.Cell>
                <IndexTable.Cell><Box paddingBlock={'500'}>{quantity}</Box> </IndexTable.Cell>
                <IndexTable.Cell><Box paddingBlock={'500'}>{status}</Box></IndexTable.Cell>
                <IndexTable.Cell><Box paddingBlock={'500'}>{fullfilment_status}</Box> </IndexTable.Cell>
                <IndexTable.Cell><Box paddingBlock={'500'}>{total_price}</Box> </IndexTable.Cell>
                <IndexTable.Cell><Box paddingBlock={'500'}>{country}</Box> </IndexTable.Cell>
                <IndexTable.Cell><Box paddingBlock={'500'}><InlineStack gap={'050'}><Button variant='plain' size='large' tone='success' icon={EditIcon} /><Button variant='plain' size='large' tone='critical' icon={DeleteIcon} /></InlineStack></Box> </IndexTable.Cell>

            </IndexTable.Row>
        ),
    );

    const SearchFilter = (value) => {
        setReload(false);
        setQueryValue(value);
    };
    return (
        <Box paddingInline={'800'}>
            <Page
                title='Dashboard'
                fullWidth
                backAction={() => { }}
            >
                <div style={{ display: "flex", gap: '10px', justifyContent: "end" }}>

                    <FiancialFilterDropdown onFiancialStatusChange={setonFiancialStatusChange} />
                    <FulfilledFilterDropdown onFulfillStatusChange={setonFulfillStatusChange} />
                    <div>
                        <Button onClick={onHandleCancel} disabled={!onFiancialStatusChange && !onFulfillStatusChange}>Cancel Filter</Button>

                    </div>
                    <div>
                        <Button onClick={handleSyncOrders}  disabled={syncOrders} >Sync Orders</Button>
                    </div>
               
                </div>

                <Card>
                    <IndexFilters
                        sortOptions={sortOptions}
                        sortSelected={sortSelected}
                        queryValue={queryValue}
                        queryPlaceholder="Searching in all"

                        onQueryChange={SearchFilter}

                        onQueryClear={() => setQueryValue('')}
                        onSort={setSortSelected}
                        primaryAction={primaryAction}
                        cancelAction={{
                            onAction: onHandleCancel,
                            disabled: false,
                            loading: false,
                        }}

                        tabs={tabs}
                        selected={selected}
                        onSelect={setSelected}
                        canCreateNewView
                        onCreateNewView={onCreateNewView}
                        filters={filters}
                        appliedFilters={appliedFilters}
                        onClearAll={handleFiltersClearAll}
                        mode={mode}
                        setMode={setMode}
                        loading={reload}
                    />
                    <IndexTable
                        condensed={useBreakpoints().smDown}
                        resourceName={resourceName}
                        itemCount={logs.length}
                        selectable={false}
                        selectedItemsCount={
                            allResourcesSelected ? 'All' : selectedResources.length
                        }
                        onSelectionChange={handleSelectionChange}
                        headings={[
                            { title: 'ID' },
                            { title: 'Order No' },
                            { title: 'customer_name' },
                            { title: 'order_name' },
                            { title: 'Quantity' },
                            { title: 'Status  ' },
                            { title: 'Fullfilment Status', },
                            { title: 'Total price' },
                            { title: 'Country' },
                            { title: 'Action' },
                        ]}
                    >
                        {rowMarkup}
                    </IndexTable>


                </Card>
            </Page>
        </Box>
    )
}

function disambiguateLabel(key, value) {
    switch (key) {
        case 'moneySpent':
            return `Money spent is between $${value[0]} and $${value[1]}`;
        case 'taggedWith':
            return `Tagged with ${value}`;
        case 'accountStatus':
            return (value).map((val) => `Customer ${val}`).join(', ');
        default:
            return value;
    }
}

function isEmpty(value) {
    if (Array.isArray(value)) {
        return value.length === 0;
    } else {
        return value === '' || value == null;
    }
}

