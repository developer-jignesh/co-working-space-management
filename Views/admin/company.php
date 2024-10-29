<?php
if (!defined('ABSPATH'))  {
    echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
    exit;
}

use App\Controllers\Company as CompanyController;

$edit_mode = isset($_GET['action']) && $_GET['action'] === 'edit' && isset($_GET['company_id']);
$company_controller = new CompanyController($edit_mode ? intval($_GET['company_id']) : null);

// Handle form submissions for adding or updating
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'name'         => sanitize_text_field($_POST['name'] ?? ''),
        'contact_name' => sanitize_text_field($_POST['contact_name'] ?? ''),
        'contact_email'=> sanitize_email($_POST['contact_email'] ?? ''),
        'contact_phone'=> sanitize_text_field($_POST['contact_phone'] ?? ''),
        'leased_space' => sanitize_text_field($_POST['leased_space'] ?? ''),
        'location_id'=> intval($_POST['location_id'] ?? 0)
    ];

    if (isset($_POST['add_company'])) {
        // Set each property in CompanyProps
        foreach ($data as $key => $value) {
            $company_controller->set_data($key, $value);
        }
        
        // Attempt to save the data and show appropriate message
        if ($company_controller->save_data()) {
            echo '<div class="updated"><p>Company added successfully!</p></div>';
        }
    }
    
    if (isset($_POST['update_company'])) {
        foreach ($data as $key => $value) {
            $company_controller->set_data($key, $value);
        }
    
        if (isset($_POST['company_id'])) {
            $company_controller->set_data('id', intval($_POST['company_id']));
        }
    
        if ($company_controller->save_data()) {
            echo '<div class="updated"><p>Company updated successfully!</p></div>';
        }
    }
    
    
    if (isset($_POST['delete_company']) && isset($_POST['company_id'])) {
        $company_id = intval($_POST['company_id']);
        $company_controller->delete_data($company_id);
        echo '<div class="updated"><p>Company deleted successfully!</p></div>';
    }
}

// Fetch all companies for display
$companies = $company_controller->get_all_companies();
$companies_per_page = 10;
$current_page = isset($_GET['paged']) ? max(1, intval($_GET['paged'])) : 1;
$total_companies = $company_controller->countCompanies();
$total_pages = ceil($total_companies / $companies_per_page);
$offset = ($current_page -1) * $companies_per_page;
$company_controller->getAllcompaniesforPagination($companies_per_page, $offset);
?>


<h1><?php _e('Manage Companies', 'coworking-text-domain'); ?></h1>

<h2><?php echo $edit_mode ? __('Edit Company', 'coworking-text-domain') : __('Add New Company', 'coworking-text-domain'); ?></h2>

<form method="post">
    <input type="hidden" name="company_id" value="<?php echo esc_attr($company_controller->get_data('id') ?? ''); ?>">
    <table class="form-table">
        <tr>
            <th><label for="name"><?php _e('Company Name', 'coworking-text-domain'); ?></label></th>
            <td><input type="text" name="name" id="name" value="<?php echo esc_attr($company_controller->get_data('name') ?? ''); ?>" required></td>
        </tr>
        <tr>
            <th><label for="contact_name"><?php _e('Contact Name', 'coworking-text-domain'); ?></label></th>
            <td><input type="text" name="contact_name" id="contact_name" value="<?php echo esc_attr($company_controller->get_data('contact_name') ?? ''); ?>" required></td>
        </tr>
        <tr>
            <th><label for="contact_email"><?php _e('Contact Email', 'coworking-text-domain'); ?></label></th>
            <td><input type="email" name="contact_email" id="contact_email" value="<?php echo esc_attr($company_controller->get_data('contact_email') ?? ''); ?>" required></td>
        </tr>
        <tr>
            <th><label for="contact_phone"><?php _e('Contact Phone', 'coworking-text-domain'); ?></label></th>
            <td><input type="text" name="contact_phone" id="contact_phone" value="<?php echo esc_attr($company_controller->get_data('contact_phone') ?? ''); ?>" required></td>
        </tr>
        <tr>
            <th><label for="leased_space"><?php _e('Leased Space', 'coworking-text-domain'); ?></label></th>
            <td><input type="number" name="leased_space" id="leased_space" value="<?php echo esc_attr($company_controller->get_data('leased_space') ?? ''); ?>" required></td>
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
        <input type="submit" name="<?php echo $edit_mode ? 'update_company' : 'add_company'; ?>" value="<?php echo $edit_mode ? __('Update Company', 'coworking-text-domain') : __('Add Company', 'coworking-text-domain'); ?>" class="button button-primary">
        
        <?php if ($edit_mode): ?>
            <!-- Add Reset Form Button -->
            <a href="<?php echo admin_url('admin.php?page=manage-companies'); ?>" class="button"><?php _e('Reset Form', 'coworking-text-domain'); ?></a>
        <?php endif; ?>
    </p>
</form>

<h2><?php _e('Existing Companies', 'coworking-text-domain'); ?></h2>
<table class="wp-list-table widefat fixed striped">
    <thead>
        <tr>
            <th><?php _e('ID', 'coworking-text-domain'); ?></th>
            <th><?php _e('Company Name', 'coworking-text-domain'); ?></th>
            <th><?php _e('Contact Name', 'coworking-text-domain'); ?></th>
            <th><?php _e('Contact Email', 'coworking-text-domain'); ?></th>
            <th><?php _e('Contact Phone', 'coworking-text-domain'); ?></th>
            <th><?php _e('Leased space', 'coworking-text-domain'); ?></th>
            <th><?php _e('Actions', 'coworking-text-domain'); ?></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($companies as $company): ?>
        <tr>
            <td><?php echo esc_html($company->id); ?></td>
            <td><?php echo esc_html($company->name); ?></td>
            <td><?php echo esc_html($company->contact_name); ?></td>
            <td><?php echo esc_html($company->contact_email); ?></td>
            <td><?php echo esc_html($company->contact_phone); ?></td>
            <td><?php echo esc_html($company->leased_space); ?></td>
            <td>
                <a href="<?php echo admin_url('admin.php?page=manage-companies&action=edit&company_id=' . esc_attr($company->id)); ?>" class="button"><?php _e('Edit', 'coworking-text-domain'); ?></a>
                <form method="post" style="display:inline;">
                    <input type="hidden" name="company_id" value="<?php echo esc_attr($company->id); ?>">
                    <input type="submit" name="delete_company" value="<?php _e('Delete', 'coworking-text-domain'); ?>" class="button button-danger">
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<!-- Pagination Links -->
<div class="pagination">
    <?php
    echo paginate_links([
        'base' => add_query_arg('paged', '%#%'),
        'format' => '',
        'current' => $current_page,
        'total' => $total_pages,
        'prev_text' => __('« Previous', 'coworking-text-domain'),
        'next_text' => __('Next »', 'coworking-text-domain'),
    ]);
    ?>
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

