<div id="journalNewEntryModal" class="journal-modal" onclick="closeJournalModal()">
    <div class="modal-content">
        <div class="modal-content-container">
            <div class="modal-header">
                <input type="text" class="modal-title" id="modalDate" value="" />
                <button class="save-btn" onclick="saveJournalEntry()"><?php echo __('Done', WP_JOURNAL_TEXT_DOMAIN); ?></button>
                <input type="hidden" name="newEntryId" id="newEntryId" value="0" />
            </div>
            <div style="position: relative;">
                <div id="modal-errors"></div>
                <textarea class="modal-textarea" id="newEntryContent" placeholder="Dictate or type your insights..."></textarea>
                <button class="mic-btn" id="startStopButton">
                    <i class="fas fa-microphone"></i>
                    <i class="fas fa-stop"></i>
                </button>
                <div class="mobile-dictate-msg">
                    <div class="mobile-dictate-msg-top"></div>
                    <div class="mobile-dictate-msg-bottom">
                        Prefer to speak? Use Dictate on your keyboard<svg width="8" height="12" viewBox="0 0 9 13" fill="none" xmlns="http://www.w3.org/2000/svg" style="margin-left: 5px;"><path d="M4.29447 8.23084C3.18029 8.23084 2.32031 7.39791 2.32031 6.16474V2.50849C2.32031 1.27532 3.18029 0.442383 4.29447 0.442383C5.40325 0.442383 6.26322 1.27532 6.26322 2.50849V6.16474C6.26322 7.39791 5.40325 8.23084 4.29447 8.23084ZM0 6.31077V5.26149C0 4.94238 0.265024 4.69358 0.589543 4.69358C0.908654 4.69358 1.17909 4.94238 1.17909 5.26149V6.27291C1.17909 8.11185 2.45553 9.32339 4.29447 9.32339C6.12801 9.32339 7.40986 8.11185 7.40986 6.27291V5.26149C7.40986 4.94238 7.67488 4.69358 7.9994 4.69358C8.32392 4.69358 8.58353 4.94238 8.58353 5.26149V6.31077C8.58353 8.62027 7.04207 10.1726 4.85156 10.3997V11.3949H6.82572C7.14483 11.3949 7.41527 11.6545 7.41527 11.9736C7.41527 12.2927 7.14483 12.5578 6.82572 12.5578H1.76322C1.4387 12.5578 1.17368 12.2927 1.17368 11.9736C1.17368 11.6545 1.4387 11.3949 1.76322 11.3949H3.73197V10.3997C1.54688 10.1726 0 8.62027 0 6.31077Z" fill="#9F9F9F"/></svg>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>