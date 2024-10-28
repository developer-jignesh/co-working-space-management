<?php
if (!defined('ABSPATH'))  {
    echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
    exit;
}
use App\Controllers\Employee as EmployeeController;

$edit_mode = isset($_GET['action']) && $_GET['action'] === 'edit' && isset($_GET['employee_id']);
$employee_id = $edit_mode ? intval($_GET['employee_id']) : null;
$employee_controller = new EmployeeController($employee_id); // Instantiate Employee controller with ID if editing

// Handle form submissions for adding or updating
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'first_name' => sanitize_text_field($_POST['first_name'] ?? ''),
        'last_name'  => sanitize_text_field($_POST['last_name'] ?? ''),
        'email'      => sanitize_email($_POST['email'] ?? ''),
        'phone'      => sanitize_text_field($_POST['phone'] ?? ''),
        'role'       => sanitize_text_field($_POST['role'] ?? ''),
        'location_id'=> intval($_POST['location_id'] ?? 0),
    ];

    if (isset($_POST['add_employee'])) {
        // Set each property in EmployeeProps
        foreach ($data as $key => $value) {
            $employee_controller->set_data($key, $value);
        }
        // Save all properties to the database
        $employee_controller->save_data();
        echo '<div class="updated"><p>Employee added successfully!</p></div>';
    }

    if (isset($_POST['update_employee'])) {
        // Set each field in EmployeeProps
        foreach ($data as $key => $value) {
            $employee_controller->set_data($key, $value);
        }
    
        // Set the ID explicitly for updating
        if (isset($_POST['employee_id'])) {
            $employee_controller->set_data('id', intval($_POST['employee_id']));
        }
    
        // Save the data (will update if ID is set)
        $employee_controller->save_data();
        echo '<div class="updated"><p>Employee updated successfully!</p></div>';
    }

    if (isset($_POST['delete_employee']) && isset($_POST['employee_id'])) {
        $employee_controller->delete_data(intval($_POST['employee_id']));
        echo '<div class="updated"><p>Employee deleted successfully!</p></div>';
    }
}

// Fetch all employees for display
$employees = $employee_controller->get_all_employees();
?>

<h1><?php _e('Manage Employees', 'coworking-text-domain'); ?></h1>

<h2><?php echo $edit_mode ? __('Edit Employee', 'coworking-text-domain') : __('Add New Employee', 'coworking-text-domain'); ?></h2>

<form method="post">
    <input type="hidden" name="employee_id" value="<?php echo esc_attr($employee_controller->get_data('id') ?? ''); ?>">
    <table class="form-table">
        <tr>
            <th><label for="first_name"><?php _e('First Name', 'coworking-text-domain'); ?></label></th>
            <td><input type="text" name="first_name" id="first_name" value="<?php echo esc_attr($employee_controller->get_data('first_name') ?? ''); ?>" required></td>
        </tr>
        <tr>
            <th><label for="last_name"><?php _e('Last Name', 'coworking-text-domain'); ?></label></th>
            <td><input type="text" name="last_name" id="last_name" value="<?php echo esc_attr($employee_controller->get_data('last_name') ?? ''); ?>" required></td>
        </tr>
        <tr>
            <th><label for="email"><?php _e('Email', 'coworking-text-domain'); ?></label></th>
            <td><input type="email" name="email" id="email" value="<?php echo esc_attr($employee_controller->get_data('email') ?? ''); ?>" required></td>
        </tr>
        <tr>
            <th><label for="phone"><?php _e('Phone', 'coworking-text-domain'); ?></label></th>
            <td><input type="text" name="phone" id="phone" value="<?php echo esc_attr($employee_controller->get_data('phone') ?? ''); ?>" required></td>
        </tr>
        <tr>
            <th><label for="role"><?php _e('Role', 'coworking-text-domain'); ?></label></th>
            <td><input type="text" name="role" id="role" value="<?php echo esc_attr($employee_controller->get_data('role') ?? ''); ?>" required></td>
        </tr>
        <tr>
            <th><label for="location_id"><?php _e('Location', 'coworking-text-domain'); ?></label></th>
            <td>
                <select name="location_id" id="location_id" required>
                    <!-- AJAX will populate options dynamically -->
                </select>
            </td>
        </tr>
    </table>
    
    <p>
        <input type="submit" name="<?php echo $edit_mode ? 'update_employee' : 'add_employee'; ?>" value="<?php echo $edit_mode ? __('Update Employee', 'coworking-text-domain') : __('Add Employee', 'coworking-text-domain'); ?>" class="button button-primary">
        
        <?php if ($edit_mode): ?>
            <a href="<?php echo admin_url('admin.php?page=manage-employees'); ?>" class="button"><?php _e('Reset Form', 'coworking-text-domain'); ?></a>
        <?php endif; ?>
    </p>
</form>

<h2><?php _e('Existing Employees', 'coworking-text-domain'); ?></h2>
<table class="wp-list-table widefat fixed striped">
    <thead>
        <tr>
            <th><?php _e('ID', 'coworking-text-domain'); ?></th>
            <th><?php _e('First Name', 'coworking-text-domain'); ?></th>
            <th><?php _e('Last Name', 'coworking-text-domain'); ?></th>
            <th><?php _e('Email', 'coworking-text-domain'); ?></th>
            <th><?php _e('Phone', 'coworking-text-domain'); ?></th>
            <th><?php _e('Role', 'coworking-text-domain'); ?></th>
            <th><?php _e('Location', 'coworking-text-domain'); ?></th>
            <th><?php _e('Actions', 'coworking-text-domain'); ?></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($employees as $employee): ?>
        <tr>
            <td><?php echo esc_html($employee->id); ?></td>
            <td><?php echo esc_html($employee->first_name); ?></td>
            <td><?php echo esc_html($employee->last_name); ?></td>
            <td><?php echo esc_html($employee->email); ?></td>
            <td><?php echo esc_html($employee->phone); ?></td>
            <td><?php echo esc_html($employee->role); ?></td>
            <td><?php echo esc_html($employee->location_id); ?></td>
            <td>
                <a href="<?php echo admin_url('admin.php?page=manage-employees&action=edit&employee_id=' . esc_attr($employee->id)); ?>" class="button"><?php _e('Edit', 'coworking-text-domain'); ?></a>
                <form method="post" style="display:inline;">
                    <input type="hidden" name="employee_id" value="<?php echo esc_attr($employee->id); ?>">
                    <input type="submit" name="delete_employee" value="<?php _e('Delete', 'coworking-text-domain'); ?>" class="button button-danger">
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<script>
    jQuery(document).ready(function($) {
    // Fetch available locations via AJAX
    function loadLocations() {
        $.ajax({
            url: ajaxurl,
            method: 'POST',
            data: {
                action: 'get_locations',
                security: '<?php echo wp_create_nonce('get_locations_nonce'); ?>'
            },
            success: function(response) {
                if (response.success) {
                    let locationSelect = $('#location_id');
                    locationSelect.empty(); // Clear existing options
                    $.each(response.data, function(index, location) {
                        locationSelect.append('<option value="' + location.id + '">' + location.name + '</option>');
                    });
                } else {
                    alert('Failed to load locations.');
                }
            }
        });
    }

    // Load locations on page load
    loadLocations();
});
</script>
