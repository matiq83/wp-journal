<div class="journal-container">
    <?php if (isset($atts['title']) && !empty($atts['title'])) { ?>
        <h1><?php echo $atts['title']; ?></h1>
    <?php } ?>
    <div class="buttons-container">
        <?php
        echo $btn_new_entry;
        ?>
    </div>
    <div class="entries-grid" id="journalEntriesGrid">
        <?php
        if (is_array($journals)) {
            foreach ($journals as $journal) {
                ?>
                <div class="entry" id="entry-<?php echo $journal->ID; ?>" onclick="updateJournalEntry(this,<?php echo $journal->ID; ?>, event)">
                    <div class="entry-header">
                        <input type="text" class="entry-date" readonly value="<?php echo $journal->post_title; //date('m/d/y', strtotime($journal->post_date));    ?>" />
                        <div class="entry-buttons">
                            <button class="delete-btn" onclick="deleteJournalEntry(this,<?php echo $journal->ID; ?>, event)">
                                <svg class="delete-btn-svg" width="16" height="20" viewBox="0 0 16 20" fill="#414141" xmlns="http://www.w3.org/2000/svg">
                                    <path class="delete-btn-svg" d="M11.56 2.57143H14.4C15.288 2.57143 16 3.33429 16 4.28571V5.43428C16 5.75143 15.76 6 15.472 6H0.528C0.232 6 0 5.74285 0 5.43428V4.28571C0 3.33429 0.72 2.57143 1.6 2.57143H4.44C4.704 2.57143 4.952 2.4 5.048 2.14286C5.52 0.891428 6.664 0 8 0C9.336 0 10.48 0.891428 10.952 2.14286C11.056 2.40857 11.296 2.57143 11.56 2.57143Z"/>
                                    <path class="delete-btn-svg" fill-rule="evenodd" clip-rule="evenodd" d="M2.10977 7.49683H13.8902C14.2406 7.49683 14.516 7.7883 14.4993 8.13807L13.9653 17.74C13.8986 19.0058 12.8473 19.9968 11.5792 19.9968H4.42081C3.15266 19.9968 2.10142 19.0058 2.03468 17.74L1.50072 8.13807C1.48403 7.7883 1.75936 7.49683 2.10977 7.49683ZM5.64724 18.4895H5.6973C6.15617 18.4645 6.51493 18.0731 6.47321 17.6068L6.05606 9.9452C6.03103 9.48717 5.63056 9.09576 5.18003 9.16238C4.72116 9.18737 4.37075 9.57877 4.39578 10.0368L4.81293 17.6984C4.83796 18.1481 5.20506 18.4895 5.64724 18.4895ZM10.3945 18.4978C10.8367 18.4978 11.2038 18.1481 11.2288 17.7067L11.6376 10.0535C11.6626 9.59543 11.3122 9.20403 10.8533 9.17904C10.4112 9.11242 10.0023 9.50383 9.97732 9.96186L9.56016 17.6234C9.53513 18.0814 9.88554 18.4729 10.3444 18.4978H10.3945Z"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                    <div class="entry-content">
                        <textarea class="entry-textarea" readonly><?php echo $journal->post_content; ?></textarea>
                    </div>
                </div>
            <?php } ?>
        <?php } ?>
    </div>
</div>