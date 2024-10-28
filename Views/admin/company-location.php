<?php
if (!defined('ABSPATH')) exit;

use App\Controllers\CompanyLocation;

$company_location_controller = new CompanyLocation();

// Check if we're in edit mode
$edit_mode = isset($_GET['action']) && $_GET['action'] === 'edit' && isset($_GET['company_id']) && isset($_GET['location_id']);
$company_location_controller = new CompanyLocation($edit_mode ? intval($_GET['company_id']) : null, $edit_mode ? intval($_GET['location_id']) : null);


// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $leased_space = isset($_POST['leased_space']) ? intval($_POST['leased_space']) : null;
$monthly_rent = isset($_POST['monthly_rent']) ? floatval($_POST['monthly_rent']) : null;
$contract_start_date = isset($_POST['contract_start_date']) ? sanitize_text_field($_POST['contract_start_date']) : null;
$contract_end_date = isset($_POST['contract_end_date']) ? sanitize_text_field($_POST['contract_end_date']) : null;

  
$data = [
    'company_id'         => intval($_POST['company_id']),
    'location_id'        => intval($_POST['location_id']),
    'leased_space'       => $leased_space,
    'monthly_rent'       => $monthly_rent,
    'contract_start_date'=> $contract_start_date,
    'contract_end_date'  => $contract_end_date,
];

    // Debug: Check if the data array is correct

    if (isset($_POST['add_company_location'])) {
        foreach ($data as $key => $value) {
            $company_location_controller->set_data($key, $value);
        }
        $company_location_controller->save_data();
        echo '<div class="updated"><p>Company-Location association added successfully!</p></div>';
    }

    if (isset($_POST['update_company_location'])) {
        // First, set the fields from the submitted data
        foreach ($data as $key => $value) {
            $company_location_controller->set_data($key, $value);
        }
        // Ensure both IDs are being set correctly
        $company_location_controller->set_data('company_id', intval($_POST['company_id']));
        $company_location_controller->set_data('location_id', intval($_POST['location_id']));
        
        // Call save_data, which will handle updating or creating
        $company_location_controller->save_data();
        echo '<div class="updated"><p>Company-Location association updated successfully!</p></div>';
    }
    
    if (isset($_POST['delete_company_location'])) {
        $company_location_controller->delete_data(intval($_POST['company_id']), intval($_POST['location_id']));
        echo '<div class="updated"><p>Company-Location association deleted successfully!</p></div>';
    }
}

// Fetch all company-location associations for display
$company_locations = $company_location_controller->get_all_company_locations();
?>

<h1><?php _e('Manage Company Locations', 'coworking-text-domain'); ?></h1>

<h2><?php echo $edit_mode ? __('Edit Company Location', 'coworking-text-domain') : __('Add New Company Location', 'coworking-text-domain'); ?></h2>

<form method="post">
    <?php wp_nonce_field('manage_company_location_nonce', 'company_location_nonce'); ?>
    <input type="hidden" name="company_id" value="<?php echo esc_attr($company_location_controller->get_data('company_id') ?? ''); ?>">
    <input type="hidden" name="location_id" value="<?php echo esc_attr($company_location_controller->get_data('location_id') ?? ''); ?>">

    <table class="form-table">
        <tr>
            <th><label for="company_id"><?php _e('Company', 'coworking-text-domain'); ?></label></th>
            <td>
                <select name="company_id" id="company_id" required>
                    <!-- AJAX will populate options dynamically -->
                </select>
            </td>
        </tr>
        <tr>
            <th><label for="location_id"><?php _e('Location', 'coworking-text-domain'); ?></label></th>
            <td>
                <select name="location_id" id="location_id" required>
                    <!-- AJAX will populate options dynamically -->
                </select>
            </td>
        </tr>
        <tr>
            <th><label for="leased_space"><?php _e('Leased Space (sq ft)', 'coworking-text-domain'); ?></label></th>
            <td><input type="number" name="leased_space" id="leased_space" value="<?php echo esc_attr($company_location_controller->get_data('leased_space') ?? ''); ?>" required></td>
        </tr>
        <tr>
            <th><label for="monthly_rent"><?php _e('Monthly Rent', 'coworking-text-domain'); ?></label></th>
            <td><input type="text" name="monthly_rent" id="monthly_rent" value="<?php echo esc_attr($company_location_controller->get_data('monthly_rent') ?? ''); ?>" required></td>
        </tr>
        <tr>
            <th><label for="contract_start_date"><?php _e('Contract Start Date', 'coworking-text-domain'); ?></label></th>
            <td><input type="date" name="contract_start_date" id="contract_start_date" value="<?php echo esc_attr($company_location_controller->get_data('contract_start_date') ?? ''); ?>" required></td>
        </tr>
        <tr>
            <th><label for="contract_end_date"><?php _e('Contract End Date', 'coworking-text-domain'); ?></label></th>
            <td><input type="date" name="contract_end_date" id="contract_end_date" value="<?php echo esc_attr($company_location_controller->get_data('contract_end_date') ?? ''); ?>" required></td>
        </tr>
    </table>

    <p>
        <input type="submit" name="<?php echo $edit_mode ? 'update_company_location' : 'add_company_location'; ?>" value="<?php echo $edit_mode ? __('Update Company Location', 'coworking-text-domain') : __('Add Company Location', 'coworking-text-domain'); ?>" class="button button-primary">

        <?php if ($edit_mode): ?>
            <a href="<?php echo admin_url('admin.php?page=manage-company-locations'); ?>" class="button"><?php _e('Reset Form', 'coworking-text-domain'); ?></a>
        <?php endif; ?>
    </p>
</form>

<h2><?php _e('Existing Company Locations', 'coworking-text-domain'); ?></h2>
<table class="wp-list-table widefat fixed striped">
    <thead>
        <tr>
            <th><?php _e('Company ID', 'coworking-text-domain'); ?></th>
            <th><?php _e('Location ID', 'coworking-text-domain'); ?></th>
            <th><?php _e('Leased Space', 'coworking-text-domain'); ?></th>
            <th><?php _e('Monthly Rent', 'coworking-text-domain'); ?></th>
            <th><?php _e('Contract Start Date', 'coworking-text-domain'); ?></th>
            <th><?php _e('Contract End Date', 'coworking-text-domain'); ?></th>
            <th><?php _e('Actions', 'coworking-text-domain'); ?></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($company_locations as $company_location): ?>
        <tr>
            <td><?php echo esc_html($company_location->company_id); ?></td>
            <td><?php echo esc_html($company_location->location_id); ?></td>
            <td><?php echo esc_html($company_location->leased_space); ?></td>
            <td><?php echo esc_html($company_location->monthly_rent); ?></td>
            <td><?php echo esc_html($company_location->contract_start_date); ?></td>
            <td><?php echo esc_html($company_location->contract_end_date); ?></td>
            <td>
                <a href="<?php echo admin_url('admin.php?page=manage-company-locations&action=edit&company_id=' . esc_attr($company_location->company_id) . '&location_id=' . esc_attr($company_location->location_id)); ?>" class="button"><?php _e('Edit', 'coworking-text-domain'); ?></a>
                <form method="post" style="display:inline;">
                    <?php wp_nonce_field('manage_company_location_nonce', 'company_location_nonce'); ?>
                    <input type="hidden" name="company_id" value="<?php echo esc_attr($company_location->company_id); ?>">
                    <input type="hidden" name="location_id" value="<?php echo esc_attr($company_location->location_id); ?>">
                    <input type="submit" name="delete_company_location" value="<?php _e('Delete', 'coworking-text-domain'); ?>" class="button button-danger">
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<script type="text/javascript">
jQuery(document).ready(function($) {
    function loadCompanies() {
        $.ajax({
            url: ajaxurl,
            method: 'POST',
            data: {
                action: 'get_companies',
                security: '<?php echo wp_create_nonce('get_companies_nonce'); ?>'
            },
            success: function(response) {
                let companySelect = $('#company_id');
                companySelect.empty();
                $.each(response.data, function(index, company) {
                    companySelect.append('<option value="' + company.id + '">' + company.name + '</option>');
                });
            }
        });
    }

    function loadLocations() {
        $.ajax({
            url: ajaxurl,
            method: 'POST',
            data: {
                action: 'get_locations',
                security: '<?php echo wp_create_nonce('get_locations_nonce'); ?>'
            },
            success: function(response) {
                let locationSelect = $('#location_id');
                locationSelect.empty();
                $.each(response.data, function(index, location) {
                    locationSelect.append('<option value="' + location.id + '">' + location.name + '</option>');
                });
            }
        });
    }

    loadCompanies();
    loadLocations();
});
</script>
