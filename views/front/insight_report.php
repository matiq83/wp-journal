<?php
if (is_array($journals) && sizeof($journals) <= $options['insight_report_limit']) {
    foreach ($journals as $journal) {
        ?>
        <h2><?php echo date('m/d/y', strtotime($journal->post_date)); ?></h2>
        <p><?php echo $journal->post_content; ?></p>
        <hr>
    <?php } ?>
    <?php
} else {
    echo $options['insight_report_limit'] . ' ' . __('Journals Required', WP_JOURNAL_TEXT_DOMAIN);
}