<?php
//exit if direct access
if(!defined("ABSPATH")) exit;
global $wpdb;
// Fetch locations and their employee counts
$location_data = $wpdb->get_results("
    SELECT l.name, COUNT(e.id) as employee_count 
    FROM {$wpdb->prefix}Location l
    LEFT JOIN {$wpdb->prefix}Employee e ON l.id = e.location_id
    GROUP BY l.id
");

// Prepare arrays for labels and data
$location_names = [];
$employee_counts = [];

foreach ($location_data as $data) {
    $location_names[] = $data->name;
    $employee_counts[] = $data->employee_count;
}
// Fetch the 5 most recent locations
$recent_locations = $wpdb->get_results("
    SELECT name, created_at 
    FROM {$wpdb->prefix}Location
    ORDER BY created_at DESC
    LIMIT 5
");
// Fetch the 5 most recent employees
$recent_employees = $wpdb->get_results("
    SELECT CONCAT(first_name, ' ', last_name) as name, created_at 
    FROM {$wpdb->prefix}Employee
    ORDER BY created_at DESC
    LIMIT 5
");
// Fetch the 5 most recent companies
$recent_companies = $wpdb->get_results("
    SELECT name, created_at 
    FROM {$wpdb->prefix}Company
    ORDER BY created_at DESC
    LIMIT 5
");

// Encode the PHP arrays to JSON so we can pass them to JavaScript
$location_names_json = json_encode($location_names);
$employee_counts_json = json_encode($employee_counts);
?>
<style>
        /* Dashboard Container */
.dashboard-container {
    display: grid;
    grid-template-columns: 1fr;
    gap: 20px;
    padding: 20px;
    background-color: #f4f6f9;
}

/* Dashboard Metrics */
.dashboard-metrics {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); /* Responsive grid */
    gap: 20px;
}

/* Metric Card */
.metric-card {
    background-color: #fff;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    text-align: center;
    font-size: 1.2em;
}

.metric-card h3 {
    font-size: 2em;
    margin: 0;
    color: #3579F6; /* Example primary color */
}

.metric-card p {
    font-size: 1.1em;
    color: #555;
}

/* Hover Effect */
.metric-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 6px 8px rgba(0,0,0,0.2);
    transition: all 0.3s ease;
}
/* Recent Activity Section */
.recent-activity {
    background-color: #fff;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    margin-top: 20px;
}

.recent-activity h2 {
    font-size: 1.8em;
    margin-bottom: 20px;
}

.recent-section {
    margin-bottom: 20px;
}

.recent-section h3 {
    font-size: 1.4em;
    margin-bottom: 10px;
}

.recent-section ul {
    list-style: none;
    padding: 0;
}

.recent-section li {
    background-color: #f4f6f9;
    padding: 10px;
    margin-bottom: 5px;
    border-radius: 4px;
    font-size: 1.1em;
}

</style>
<div class="dashboard-container">
    <div class="dashboard-metrics">
        <div class="metric-card">
            <h3><?php echo $total_locations; ?></h3>
            <p>Total Locations</p>
        </div>
        <div class="metric-card">
            <h3><?php echo $total_employees; ?></h3>
            <p>Total Employees</p>
        </div>
        <div class="metric-card">
            <h3><?php echo $total_companies; ?></h3>
            <p>Total Companies</p>
        </div>
        <div class="metric-card">
            <h3><?php echo $occupied_space; ?> <br><br>/ <br><br> <?php echo $total_space; ?></h3>
            <br>
            <p>Occupied / Available Space (sq ft)</p>
        </div>
        <div class="metric-card">
            <h3><?php echo $monthly_rent; ?></h3>
            <p>Total Monthly Rent</p>
        </div>
    </div>
</div>
<div class="dashboard-chart">
    <canvas id="employeeLocationChart" width="400" height="100"></canvas>
</div>
<div class="recent-activity">
    <h2><?php _e('Recent Activity', 'coworking-text-domain'); ?></h2>

    <div class="recent-section">
        <h3><?php _e('Recently Added Locations', 'coworking-text-domain'); ?></h3>
        <ul>
            <?php if ($recent_locations): ?>
                <?php foreach ($recent_locations as $location): ?>
                    <li><?php echo esc_html($location->name); ?> (<?php echo date('Y-m-d', strtotime($location->created_at)); ?>)</li>
                <?php endforeach; ?>
            <?php else: ?>
                <li><?php _e('No recent locations found', 'coworking-text-domain'); ?></li>
            <?php endif; ?>
        </ul>
    </div>

    <div class="recent-section">
        <h3><?php _e('Recently Added Employees', 'coworking-text-domain'); ?></h3>
        <ul>
            <?php if ($recent_employees): ?>
                <?php foreach ($recent_employees as $employee): ?>
                    <li><?php echo esc_html($employee->name); ?> (<?php echo date('Y-m-d', strtotime($employee->created_at)); ?>)</li>
                <?php endforeach; ?>
            <?php else: ?>
                <li><?php _e('No recent employees found', 'coworking-text-domain'); ?></li>
            <?php endif; ?>
        </ul>
    </div>

    <div class="recent-section">
        <h3><?php _e('Recently Added Companies', 'coworking-text-domain'); ?></h3>
        <ul>
            <?php if ($recent_companies): ?>
                <?php foreach ($recent_companies as $company): ?>
                    <li><?php echo esc_html($company->name); ?> (<?php echo date('Y-m-d', strtotime($company->created_at)); ?>)</li>
                <?php endforeach; ?>
            <?php else: ?>
                <li><?php _e('No recent companies found', 'coworking-text-domain'); ?></li>
            <?php endif; ?>
        </ul>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var ctx = document.getElementById('employeeLocationChart').getContext('2d');
    
    // Get the data from PHP (passed as JSON)
    var locationNames = <?php echo $location_names_json; ?>;
    var employeeCounts = <?php echo $employee_counts_json; ?>;
    
    var myChart = new Chart(ctx, {
        type: 'bar', // Define the chart type (bar chart)
        data: {
            labels: locationNames, // X-axis labels (locations)
            datasets: [{
                label: 'Number of Employees',
                data: employeeCounts, // Y-axis data (employee counts)
                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
});
</script>
