<?php
// Add custom Theme Functions here

// Function to update the status of pending orders after a specified time period
function update_pending_orders_status() {
    $pending_orders = wc_get_orders( array(
        'status'        => 'pending', // Select the "pending" status
        'date_created'  => '<' . ( time() - 3 * 60 ),
        'limit'         => -1, // Get all pending orders
    ) );

    foreach ( $pending_orders as $order ) {
        if ( $order->get_date_created()->getTimestamp() < ( time() - 3 * 60 ) ) {
            $order->update_status( 'cancelled' ); // Update the order status to "cancelled"
        } else {
            error_log( 'Order ' . $order->get_id() . ' does not meet the condition.' );
        }
    }
}

// Schedule the update function to run periodically using the cron job
function schedule_pending_orders_status_update() {
    if ( ! wp_next_scheduled( 'update_pending_orders_status' ) ) {
        wp_schedule_event( time(), 'hourly', 'update_pending_orders_status' ); // You can modify the time interval here as needed
    }
}
add_action( 'wp', 'schedule_pending_orders_status_update' );

// Execute the function to update the status of pending orders
function execute_pending_orders_status_update() {
    update_pending_orders_status();
}
add_action( 'update_pending_orders_status', 'execute_pending_orders_status_update' );
add_action( 'wp_loaded', 'execute_pending_orders_status_update' );
