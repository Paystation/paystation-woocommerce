//Check if the necessary WooCommerce Blocks objects are available.
function paystation_woocommerce_payment_gateway() {
    if ( typeof window.wc.wcBlocksRegistry === 'undefined' || typeof window.wc.wcSettings === 'undefined' ) {
        return;
    }

    // Import necessary functions from the blocks registry and data store.
    const { registerPaymentMethod } = window.wc.wcBlocksRegistry;
    const { getPaymentMethodData } = window.wc.wcSettings;
    const {
        createElement: h,
        Fragment
    } = wp.element;

    /**
     * Get the settings for our payment gateway from the localized data.
     * The data is set from the PHP backend using wp_localize_script.
     */
    const settings = getPaymentMethodData( 'threeparty' ) || {};
    const { title, description, enabled, logo } = settings;

    /**
     * This component defines the content displayed when the payment method is selected.
     */
    const Content = () => {
        return h( 'p', null,  description || 'You will be redirected to Paystation Payment Gateway to complete your purchase.' );
    };

    const Label = () => {
        return h(
            'div',
            {
                // Use flexbox to align the icon and text nicely.
                style: { display: 'flex', alignItems: 'center' }
            },
            h( 'span', null, title || 'Credit card — Pay using Paystation'  ),
            h( 'img', {
                src: logo,
                alt: 'Paystation Logo',
                // Add a little margin to separate the icon from the text.
                style: { marginLeft: '8px' }
            })
        );
    };

    /**
     * Register the payment method with the WooCommerce Blocks registry.
     */
    window.wc.wcBlocksRegistry.registerPaymentMethod( {
        // A unique name for your payment method. Must match the ID from the PHP class.
        name: 'threeparty',
        // The label shown on the checkout page.
        label: h( Label, null),
        // The content component that renders the method's details.
        content: h( Content, null ),
        // This component allows for editing the payment method in the checkout block.
        // For a simple gateway, we can leave this as a Fragment.
        edit: h( Fragment, null ),
        // The configuration data. This is what we pass from the backend.
        ariaLabel: title || 'Credit card — Pay using Paystation',
        // This function determines whether the payment method should be displayed.
        // We check the 'enabled' setting passed from our PHP class.
        canMakePayment: () => (enabled === 'yes'),
    } );
}

setTimeout(function() {
    paystation_woocommerce_payment_gateway()
}, 200);

