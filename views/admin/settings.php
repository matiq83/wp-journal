<div class="wrap">
    <h2><?php esc_html_e(WP_JOURNAL_PLUGIN_NAME . ' Settings', WP_JOURNAL_TEXT_DOMAIN); ?></h2>
    <hr>
    <div class="ajax-message-container"></div>
    <form method="post" class="frm_wp_journal frm_wp_journal_bootstrap ajax_save" action="wp_journal_save_options" enctype="multipart/form-data">
        <input type="hidden" name="frm_section" id="frm_section" value="" />
        <div class="row gutters-sm">
            <!-- Navigation -->
            <div class="col-md-2 d-none d-md-block">
                <div class="cardNav">
                    <div class="card-body">
                        <nav class="nav flex-column nav-pills nav-gap-y-1">
                            <?php
                            $link1 = '<a href="#misc_settings" data-toggle="tab" class="nav-item nav-link has-icon nav-link-faded active">';
                            $link1 .= '<svg fill="#ffffff" width="24px" height="24px" viewBox="0 0 16 16" xmlns="http://www.w3.org/2000/svg"><path d="M9 13.829A3.004 3.004 0 0 0 11 11a3.003 3.003 0 0 0-2-2.829V0H7v8.171A3.004 3.004 0 0 0 5 11c0 1.306.836 2.417 2 2.829V16h2v-2.171zm-5-6A3.004 3.004 0 0 0 6 5a3.003 3.003 0 0 0-2-2.829V0H2v2.171A3.004 3.004 0 0 0 0 5c0 1.306.836 2.417 2 2.829V16h2V7.829zm10 0A3.004 3.004 0 0 0 16 5a3.003 3.003 0 0 0-2-2.829V0h-2v2.171A3.004 3.004 0 0 0 10 5c0 1.306.836 2.417 2 2.829V16h2V7.829zM12 6V4h2v2h-2zM2 6V4h2v2H2zm5 6v-2h2v2H7z" fill-rule="evenodd"/></svg>';
                            $link1 .= __('Misc. Settings', WP_JOURNAL_TEXT_DOMAIN);
                            $link1 .= '</a>';
                            ?>
                            <?php echo $link1; ?>
                        </nav>
                    </div>
                </div>
            </div>

            <div class="col-md-9">
                <div class="card">
                    <!-- Navigation Tabs -->
                    <div class="card-header border-bottom mb-3 d-flex d-md-none">
                        <ul class="nav nav-tabs card-header-tabs nav-gap-x-1" role="tablist">
                            <li class="nav-item">
                                <?php echo $link1; ?>
                            </li>
                        </ul>
                    </div>

                    <div class="card-body tab-content">
                        <!-- Content 1 -->
                        <div class="tab-pane active" id="misc_settings">
                            <h2><?php echo __('Misc. Settings', WP_JOURNAL_TEXT_DOMAIN); ?></h2>
                            <hr>
                            <?php /* ?>
                              <div class="form-group">
                              <label><?php echo __('Insight report limit', WP_JOURNAL_TEXT_DOMAIN); ?></label>
                              <?php $value = isset($options['insight_report_limit']) ? $options['insight_report_limit'] : "2"; ?>
                              <input type="number" name="insight_report_limit" value="<?php echo $value; ?>">
                              </div>
                              <?php */ ?>
                            <div class="form-group">
                                <label><?php echo __('Auto save entry after how many key press?', WP_JOURNAL_TEXT_DOMAIN); ?></label>
                                <?php $value = isset($options['auto_save_limit']) ? $options['auto_save_limit'] : "5"; ?>
                                <input type="number" name="auto_save_limit" value="<?php echo $value; ?>">
                            </div>
                            <div class="form-group">
                                <label><?php echo __('Open AI API Key', WP_JOURNAL_TEXT_DOMAIN); ?></label>
                                <?php $value = isset($options['openai_api_key']) ? $options['openai_api_key'] : ""; ?>
                                <input type="text" name="openai_api_key" value="<?php echo $value; ?>">
                            </div>
                            <div class="btnsaveContainer">
                                <input type="submit" data-section="post_types" name="btnsave" value="<?php echo __('Update Settings', WP_JOURNAL_TEXT_DOMAIN); ?>" class="btn btn-primary btnsave">
                                <div class="lds-ellipsis ajax-loader"><div></div><div></div><div></div><div></div></div>
                            </div>
                        </div>


                    </div>
                </div>
            </div>
        </div>
    </form>
</div><!-- .wrap -->