<?php
if (!defined('ABSPATH')) exit;

use App\Controllers\Location;

$location_controller = new Location();

// Check if we are in edit mode
$edit_mode = isset($_GET['action']) && $_GET['action'] === 'edit' && isset($_GET['location_id']);
 $location_controller= new Location($edit_mode ? intval($_GET['location_id']) : null);


// Handle form submissions for adding, updating, or deleting locations
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verify nonce
    if (!isset($_POST['location_nonce']) || !wp_verify_nonce($_POST['location_nonce'], 'manage_location_nonce')) {
        die('Security check failed'); // Handle this in a user-friendly way
    }

    $data = [
        'name'      => sanitize_text_field($_POST['name'] ?? ''),
        'address'   => sanitize_text_field($_POST['address'] ?? ''),
        'city'      => sanitize_text_field($_POST['city'] ?? ''),
        'state'     => sanitize_text_field($_POST['state'] ?? ''),
        'zip'       => sanitize_text_field($_POST['zip'] ?? ''),
        'country'   => sanitize_text_field($_POST['country'] ?? ''),
        'capacity'  => intval($_POST['capacity'] ?? 0),
        'amenities' => sanitize_text_field($_POST['amenities'] ?? ''),
    ];

    if (isset($_POST['add_location'])) {
        foreach ($data as $key => $value) {
            $location_controller->set_data($key, $value);
        }
        $location_controller->save_data();
        echo '<div class="updated"><p>Location added successfully!</p></div>';
    }

    if (isset($_POST['update_location'])) {
        foreach ($data as $key => $value) {
            $location_controller->set_data($key, $value);
        }
        if (isset($_POST['location_id'])) {
            $location_controller->set_data('id', intval($_POST['location_id']));
        }
        $location_controller->save_data();
        echo '<div class="updated"><p>Location updated successfully!</p></div>';
    }

    if (isset($_POST['delete_location']) && isset($_POST['location_id'])) {
        $location_controller->delete_data(intval($_POST['location_id']));
        echo '<div class="updated"><p>Location deleted successfully!</p></div>';
    }
}

// Fetch all locations for display
$locations = $location_controller->get_all_locations();
?>

<h1><?php _e('Manage Locations', 'coworking-text-domain'); ?></h1>

<h2><?php echo $edit_mode ? __('Edit Location', 'coworking-text-domain') : __('Add New Location', 'coworking-text-domain'); ?></h2>

<form method="post">
    <?php wp_nonce_field('manage_location_nonce', 'location_nonce'); ?>
    <input type="hidden" name="location_id" value="<?php echo esc_attr($location_controller->get_data('id') ?? ''); ?>">
    <table class="form-table">
        <tr>
            <th><label for="name"><?php _e('Name', 'coworking-text-domain'); ?></label></th>
            <td><input type="text" name="name" id="name" value="<?php echo esc_attr($location_controller->get_data('name') ?? ''); ?>" required></td>
        </tr>
        <tr>
            <th><label for="address"><?php _e('Address', 'coworking-text-domain'); ?></label></th>
            <td><input type="text" name="address" id="address" value="<?php echo esc_attr($location_controller->get_data('address') ?? ''); ?>" required></td>
        </tr>
        <tr>
            <th><label for="city"><?php _e('City', 'coworking-text-domain'); ?></label></th>
            <td><input type="text" name="city" id="city" value="<?php echo esc_attr($location_controller->get_data('city') ?? ''); ?>" required></td>
        </tr>
        <tr>
            <th><label for="state"><?php _e('State', 'coworking-text-domain'); ?></label></th>
            <td><input type="text" name="state" id="state" value="<?php echo esc_attr($location_controller->get_data('state') ?? ''); ?>" required></td>
        </tr>
        <tr>
            <th><label for="zip"><?php _e('ZIP', 'coworking-text-domain'); ?></label></th>
            <td><input type="text" name="zip" id="zip" value="<?php echo esc_attr($location_controller->get_data('zip') ?? ''); ?>" required></td>
        </tr>
        <tr>
            <th><label for="country"><?php _e('Country', 'coworking-text-domain'); ?></label></th>
            <td><input type="text" name="country" id="country" value="<?php echo esc_attr($location_controller->get_data('country') ?? ''); ?>" required></td>
        </tr>
        <tr>
            <th><label for="capacity"><?php _e('Capacity', 'coworking-text-domain'); ?></label></th>
            <td><input type="number" name="capacity" id="capacity" value="<?php echo esc_attr($location_controller->get_data('capacity') ?? ''); ?>" required></td>
        </tr>
        <tr>
            <th><label for="amenities"><?php _e('Amenities', 'coworking-text-domain'); ?></label></th>
            <td><input type="text" name="amenities" id="amenities" value="<?php echo esc_attr($location_controller->get_data('amenities') ?? ''); ?>" required></td>
        </tr>
    </table>
    <p>
        <input type="submit" name="<?php echo $edit_mode ? 'update_location' : 'add_location'; ?>" value="<?php echo $edit_mode ? __('Update Location', 'coworking-text-domain') : __('Add Location', 'coworking-text-domain'); ?>" class="button button-primary">

        <?php if ($edit_mode): ?>
            <a href="<?php echo admin_url('admin.php?page=manage-locations'); ?>" class="button"><?php _e('Reset Form', 'coworking-text-domain'); ?></a>
        <?php endif; ?>
    </p>
</form>

<h2><?php _e('Existing Locations', 'coworking-text-domain'); ?></h2>
<table class="wp-list-table widefat fixed striped">
    <thead>
        <tr>
            <th><?php _e('ID', 'coworking-text-domain'); ?></th>
            <th><?php _e('Name', 'coworking-text-domain'); ?></th>
            <th><?php _e('Address', 'coworking-text-domain'); ?></th>
            <th><?php _e('City', 'coworking-text-domain'); ?></th>
            <th><?php _e('Actions', 'coworking-text-domain'); ?></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($locations as $location): ?>
        <tr>
            <td><?php echo esc_html($location->id); ?></td>
            <td><?php echo esc_html($location->name); ?></td>
            <td><?php echo esc_html($location->address); ?></td>
            <td><?php echo esc_html($location->city); ?></td>
            <td>
                <a href="<?php echo admin_url('admin.php?page=manage-locations&action=edit&location_id=' . esc_attr($location->id)); ?>" class="button"><?php _e('Edit', 'coworking-text-domain'); ?></a>
                <form method="post" style="display:inline;">
                    <?php wp_nonce_field('manage_location_nonce', 'location_nonce'); ?>
                    <input type="hidden" name="location_id" value="<?php echo esc_attr($location->id); ?>">
                    <input type="submit" name="delete_location" value="<?php _e('Delete', 'coworking-text-domain'); ?>" class="button button-danger">
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
