<ul style='list-style-type: none; text-align: left; width: 100%; margin-left: 1px;'>
    <?php
    foreach ($material->getActivities() as $key => $activity) {
        echo "<li>".sprintf('[%s] - %s', $key, $activity['name'])."</li>";
    }
    ?>
</ul>
