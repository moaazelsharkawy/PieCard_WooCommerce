<?php
// Function to update the status of pending orders after a specified time period and delay the deletion of cancelled orders after an additional time period
function update_pending_orders_status() {
$pending_orders = wc_get_orders( array(
'status' => 'pending', // Select the status "pending"
'date_created' => '<' . ( time() - 3 * 60 ),
'limit' => -1, // Get all pending orders
) );
    foreach ( $pending_orders as $order ) {
    if ( $order->get_date_created()->getTimestamp() < ( time() - 3 * 60 ) ) {
        $order->update_status( 'cancelled' ); // Update the order status to "cancelled"
        
        // Set a time period to delay the deletion of cancelled orders after they are updated to "cancelled" for an additional three days
        $delete_delay = 3 * 24 * 60 * 60; // Set the time period in seconds (three days)

        // Create a scheduled event to delete the order after the specified period has elapsed
        wp_schedule_single_event( time() + $delete_delay, 'delete_cancelled_order', array( $order->get_id() ) );
    } else {
        error_log( 'Order ' . $order->get_id() . ' does not meet the condition.' );
    }
}
// Function to update the status of pending orders after a specified time period and delay the deletion of cancelled orders after an additional time period
function update_pending_orders_status() {
$pending_orders = wc_get_orders( array(
'status' => 'pending', // Select the status "pending"
'date_created' => '<' . ( time() - 3 * 60 ),
'limit' => -1, // Get all pending orders
) );

php

foreach ( $pending_orders as $order ) {
    if ( $order->get_date_created()->getTimestamp() < ( time() - 3 * 60 ) ) {
        $order->update_status( 'cancelled' ); // Update the order status to "cancelled"
        
        // Set a time period to delay the deletion of cancelled orders after they are updated to "cancelled" for an additional three days
        $delete_delay = 3 * 24 * 60 * 60; // Set the time period in seconds (three days)

        // Create a scheduled event to delete the order after the specified period has elapsed
        wp_schedule_single_event( time() + $delete_delay, 'delete_cancelled_order', array( $order->get_id() ) );
    } else {
        error_log( 'Order ' . $order->get_id() . ' does not meet the condition.' );
    }
}

}

// Schedule the update function periodically using a cron job
function schedule_pending_orders_status_update() {
if ( ! wp_next_scheduled( 'update_pending_orders_status' ) ) {
wp_schedule_event( time(), 'hourly', 'update_pending_orders_status' ); // Modify the time here as needed
}
}
add_action( 'wp', 'schedule_pending_orders_status_update' );

// Execute the update function
function execute_pending_orders_status_update() {
update_pending_orders_status();
}
add_action( 'update_pending_orders_status', 'execute_pending_orders_status_update' );
add_action( 'wp_loaded', 'execute_pending_orders_status_update' );

// Function to delete cancelled orders after a specified time period from the time they were updated to "cancelled"
function delete_cancelled_order( $order_id ) {
wp_delete_post( $order_id, true ); // Delete the cancelled order permanently from the database
}
add_action( 'delete_cancelled_order', 'delete_cancelled_order' );
