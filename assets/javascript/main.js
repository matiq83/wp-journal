// JavaScript Document
jQuery(document).ready(function($) {
    $(".frm_wp_journal.ajax_save").submit(function(e) {

        e.preventDefault(); // avoid to execute the actual submit of the form.

        var form = $(this);
        var action = form.attr('action');
        var data = form.serializeArray(); // serializes the form's elements.
        
        $(".frm_wp_journal .btnsave").hide();
        $(".frm_wp_journal .ajax-loader").show();
       
        data.push({ name:'action', value:action }); 
        
        $(".wp_journal_checkbox_field").each(function(){
            data.push({ name:$(this).attr("name"), value:$(this).val() }); 
        });
        
        $.ajax({
            type: "POST",
            dataType: 'json',
            url: wp_journal_data.ajaxurl,
            data: data, //data: {action: 'wp_journal_load_action'},
            success: function(response) {
                $(".ajax-message-container").html(response.message);
                $(".ajax-message-container").slideDown();
                setTimeout(function(){$(".ajax-message-container").slideUp();},3000);
            },
            error: function (xhr, status, error) {
                alert("There is some error to connect with server. Please try again.");
            }
        }).always(function(){  
            $(".frm_wp_journal .btnsave").show();
            $(".frm_wp_journal .ajax-loader").hide();
        });
    });
    
    if( $(".frm_wp_journal .btnsave").length ) {
        $(".frm_wp_journal .btnsave").click(function(){
            $(".frm_wp_journal #frm_section").val($(this).attr('data-section'));
        });
    }
    
    if( $(".wp_journal_checkbox_field").length ) {
        $(".wp_journal_checkbox_field").change(function(){
            if( $(this).is(':checked') ) {
                $(this).val($(this).attr("data-checked-val"));
            }else{
                $(this).val($(this).attr("data-unchecked-val"));
            }
        });
        $(".wp_journal_checkbox_field").trigger("change");
    }
    
    if( $(".wp_journal_wp_image_upload").length ) {
        $(".wp_journal_wp_image_upload").click(function(){
            wp_journal_wp_image_upload($(this));
            return false;
        });
    }
    
    if( $(".wp_journal_selects2").length ) {        
        $(".wp_journal_selects2").select2();
    }
    
    if( $("#journalNewEntryModal .modal-content").length ) {
        $('#journalNewEntryModal .modal-content').click(function(event){
            event.stopPropagation();
        });
        
        $(document).keyup(function(e) {
            if (e.key === "Escape") {
                closeJournalModal();
            }
        });
        
        const startStopButton = document.getElementById('startStopButton');
        const output = document.getElementById('newEntryContent');
                
        let mediaRecorder;
        let stream;
        let audioChunks = [];
        let startStopStatus = 'stop';
        
        startStopButton.onclick = async () => {
            startStopButton.classList.toggle("mic-stop")
            if (startStopStatus === 'start' && mediaRecorder && mediaRecorder.state !== 'inactive') {
                startStopStatus = 'stop';                
                mediaRecorder.stop();
                stream.getTracks().forEach( track => track.stop() );
                //showJournalError('Transcribing...');
            }else{
                startStopStatus = 'start';
                audioChunks = []; // Reset audio chunks
                stream = await navigator.mediaDevices.getUserMedia({ audio: true });
                mediaRecorder = new MediaRecorder(stream);

                mediaRecorder.ondataavailable = (event) => {
                    audioChunks.push(event.data);
                };

                mediaRecorder.onstop = () => {
                    const audioBlob = new Blob(audioChunks, { type: 'audio/wav' });
                    sendAudioToServer(audioBlob,startStopButton,output);
                };

                mediaRecorder.start();
                //showJournalError('Recording...');
            }            
        };
        
        $("#journalNewEntryModal #newEntryContent").on('input',function(e){
            if( wp_journal_data.auto_save_start >= wp_journal_data.auto_save_limit ) {
                saveJournalEntry(false);
                wp_journal_data.auto_save_start = 0;
            }else{
                wp_journal_data.auto_save_start++;
            }
        });
        window.visualViewport.addEventListener('resize', detectMobileKeyboardOpen);
    }
});

function detectMobileKeyboardOpen() {
    
    let $ = jQuery;
    const MIN_KEYBOARD_HEIGHT = 300;
    const PARENTS_MARG_PADDING = 154;
    const isMobile = /iPhone|iPad|iPod|Android/i.test(navigator.userAgent);
    const isKeyboardOpen = isMobile && window.screen.height - MIN_KEYBOARD_HEIGHT > window.visualViewport.height;
    const textAreaHeight = window.visualViewport.height - PARENTS_MARG_PADDING - $("#journalNewEntryModal .modal-header").height();
    
    if( isMobile ) { 
        $("#journalNewEntryModal .modal-content").addClass('modal-content-mobile');
        if(isKeyboardOpen) {
            //$('html, body').css("position", 'fixed');
            $("#journalNewEntryModal .modal-content").removeClass('modal-content-full-height');
        }else{
            //$('html, body').css("position", 'static');
            $("#journalNewEntryModal .modal-content").addClass('modal-content-full-height');        
        }   
        $("#newEntryContent").height(textAreaHeight);            
    }
}

function formatText(text) {
    const segments = text.split(/(?<=[.!?])\s+/);
    let formattedText = '';
    let currentLine = '';

    segments.forEach(segment => {
        if (currentLine.length + segment.length > 100) {  // Adjust 100 to your preferred line length
            formattedText += currentLine.trim() + '\n';
            currentLine = '';
        }
        currentLine += segment + ' ';

        if (/[.!?]$/.test(segment)) {
            formattedText += currentLine.trim() + '\n';
            currentLine = '';
        }
    });

    if (currentLine) {
        formattedText += currentLine.trim();
    }

    return formattedText.trim();
}

function sendAudioToServer(audioBlob, startStopButton,output) {
    
    hideJournalError();
    startStopButton.disabled = true;
    const formData = new FormData();
    formData.append('audio', audioBlob, 'recording.wav');

    fetch(wp_journal_data.ajaxurl+'?action=get_transcribe', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(response => {
        if(!response.error) {
            hideJournalError();
            const formattedText = formatText(response.message);
            output.value += formattedText;
            saveJournalEntry(false);
        }else{
            showJournalError(response.message);
        }
        startStopButton.disabled = false;
        console.log('Transcription complete!');
    })
    .catch(error => {
        showJournalError(error);
        startStopButton.disabled = false;
    });
}

function showJournalError(error) {
    
    let $ = jQuery;
    $(".journal-modal #modal-errors").html(error);
    $(".journal-modal #modal-errors").slideDown();
}

function hideJournalError() {
    
    let $ = jQuery;
    $(".journal-modal #modal-errors").html('');
    $(".journal-modal #modal-errors").hide();
}

function openJournalModal(setNull=true) {
    
    let $ = jQuery;
    
    if( setNull ) {
        updateJournalModalData('',getCurrentDate(),0);
    }
    detectMobileKeyboardOpen();
    $("#journalNewEntryModal").fadeIn();
    $("#newEntryContent").focus();
    
    $('html, body').css({
        overflow: 'hidden',
        height: '100%'
    });
    return false;
}

function getCurrentDate() {
    
    return new Date().toLocaleDateString('en-US', {month: 'numeric', day: 'numeric', year: '2-digit'});
}

function closeJournalModal() {
    
    let $ = jQuery;
    $('#journalNewEntryModal').fadeOut();
    $("#journalNewEntryModal #newEntryContent").val('');
    
    $('html, body').css({
        position: 'static',
        overflow: 'auto',
        height: 'auto'
    });
}

function saveJournalEntry(autoClose=true) {
    let $ = jQuery;
    let content = $("#journalNewEntryModal #newEntryContent").val();
    let title = $("#journalNewEntryModal #modalDate").val();
    let id = $("#journalNewEntryModal #newEntryId").val();
    if( content === "" && autoClose ) {
        closeJournalModal();
        return;
    }
    hideJournalError();
    $.ajax({
        type: "POST",
        dataType: 'json',
        url: wp_journal_data.ajaxurl,
        data: {action: 'save_journal', content: content, title: title, id: id},
        success: function(response) {
            if( !response.error ) {
                if( $("#journalEntriesGrid").length ) {
                    if( parseInt(id) === 0 ) {
                        addJournalEntry(getCurrentDate(), content, response);
                        $("#journalNewEntryModal #newEntryId").val(response.id);
                    }else{
                        $("#entry-"+response.id).find('.entry-textarea').val(content);
                        $("#entry-"+response.id).find('.entry-date').val(title);
                    }
                }
                if( autoClose ) {
                    closeJournalModal();
                }
            }else{
                showJournalError(response.message);
            }                
        },
        error: function (xhr, status, error) {
            showJournalError("There is some error to connect with server. Please try again.");
        }
    }).always(function(){  

    });
}

function updateJournalModalData(journalText, journalDate, id) {
    
    let $ = jQuery;
    
    $("#journalNewEntryModal #newEntryContent").val(journalText);
    $('#journalNewEntryModal #modalDate').val(journalDate);
    $("#journalNewEntryModal #newEntryId").val(id);
}

function updateJournalEntry(obj,id, event) {
    
    let $ = jQuery;
    let parent = $(obj);//.parent().parent();//.parent();
    
    if (event.target.getAttribute("class") === 'delete-btn-svg' || event.target.getAttribute("class") === 'delete-btn') return;
    
    let journalText = parent.find('.entry-textarea').val();
    let journalDate = parent.find('.entry-date').val();
    updateJournalModalData(journalText,journalDate,id);    
    openJournalModal(false);   
}

// Placeholder function to add an entry to the grid
function addJournalEntry(date, content, response) {
    
    const entryHtml = `
        <div class="entry" id="entry-${response.id}" onclick="updateJournalEntry(this,${response.id},event)">
            <div class="entry-header">
                <input type="text" readonly class="entry-date" value="${date}" />
                <div class="entry-buttons">
                    <button class="delete-btn" onclick="deleteJournalEntry(this,${response.id},event)">
                        <svg class="delete-btn-svg" fill="#414141" width="16" height="20" viewBox="0 0 16 20" xmlns="http://www.w3.org/2000/svg">
                            <path class="delete-btn-svg" d="M11.56 2.57143H14.4C15.288 2.57143 16 3.33429 16 4.28571V5.43428C16 5.75143 15.76 6 15.472 6H0.528C0.232 6 0 5.74285 0 5.43428V4.28571C0 3.33429 0.72 2.57143 1.6 2.57143H4.44C4.704 2.57143 4.952 2.4 5.048 2.14286C5.52 0.891428 6.664 0 8 0C9.336 0 10.48 0.891428 10.952 2.14286C11.056 2.40857 11.296 2.57143 11.56 2.57143Z"/>
                            <path class="delete-btn-svg" fill-rule="evenodd" clip-rule="evenodd" d="M2.10977 7.49683H13.8902C14.2406 7.49683 14.516 7.7883 14.4993 8.13807L13.9653 17.74C13.8986 19.0058 12.8473 19.9968 11.5792 19.9968H4.42081C3.15266 19.9968 2.10142 19.0058 2.03468 17.74L1.50072 8.13807C1.48403 7.7883 1.75936 7.49683 2.10977 7.49683ZM5.64724 18.4895H5.6973C6.15617 18.4645 6.51493 18.0731 6.47321 17.6068L6.05606 9.9452C6.03103 9.48717 5.63056 9.09576 5.18003 9.16238C4.72116 9.18737 4.37075 9.57877 4.39578 10.0368L4.81293 17.6984C4.83796 18.1481 5.20506 18.4895 5.64724 18.4895ZM10.3945 18.4978C10.8367 18.4978 11.2038 18.1481 11.2288 17.7067L11.6376 10.0535C11.6626 9.59543 11.3122 9.20403 10.8533 9.17904C10.4112 9.11242 10.0023 9.50383 9.97732 9.96186L9.56016 17.6234C9.53513 18.0814 9.88554 18.4729 10.3444 18.4978H10.3945Z"/>
                        </svg>
                    </button>
                </div>
            </div>
            <div class="entry-content">
                <textarea class="entry-textarea" readonly>${content}</textarea>
            </div>
        </div>
    `;
    document.getElementById('journalEntriesGrid').insertAdjacentHTML('afterbegin', entryHtml);
}

function deleteJournalEntry(button,id, event) {
    
    let $ = jQuery;
    
    if( confirm('Are you sure you want to delete it?') ) {
        $.ajax({
            type: "POST",
            dataType: 'json',
            url: wp_journal_data.ajaxurl,
            data: {action: 'delete_journal', id: id},
            success: function(response) {
                if( !response.error ) {
                    button.closest('.entry').remove();
                }else{
                    alert(response.message);
                }                
            },
            error: function (xhr, status, error) {
                alert("There is some error to connect with server. Please try again.");
            }
        }).always(function(){  
            
        });
    }
}
            
/*
 * Function to remove the uploaded image
 * @param {DOM Object} obj
 */
function wp_journal_remove_wp_image( obj, btn_upload_title='Upload image' ) {
    
    var $ = jQuery;
    
    const button = $(obj);
    button.next().val( '' ); // emptying the hidden field
    button.hide().prev().addClass( 'button' ).html( btn_upload_title ); // replace the image with text
    
    return false;
}

/*
 * Function to show the image upload
 * @param {type} obj
 */
function wp_journal_wp_image_upload( obj, type='image' ) {
    
    const button = obj;
    const imageId = button.next().next().val();

    const customUploader = wp.media({
            title: 'Insert '+type, // modal window title
            library : {
                // uploadedTo : wp.media.view.settings.post.id, // attach to the current post?
                type : type
            },
            button: {
                text: 'Use this '+type // button label text
            },
            multiple: false
    }).on( 'select', function() { // it also has "open" and "close" events
            const attachment = customUploader.state().get( 'selection' ).first().toJSON();
            button.removeClass( 'button' ).html( '<img src="' + attachment.url + '">'); // add image instead of "Upload Image"
            button.next().show(); // show "Remove image" link
            button.next().next().val( attachment.id ); // Populate the hidden field with image ID
            customUploader.close();
    });

    // already selected images
    customUploader.on( 'open', function() {

            if( imageId ) {
              const selection = customUploader.state().get( 'selection' )
              attachment = wp.media.attachment( imageId );
              attachment.fetch();
              selection.add( attachment ? [attachment] : [] );
            }

    });

    customUploader.open();
    
    return false;
}

/*
 * Function to delete the Dom row
 */
function wp_journal_del_row( row_id ) {
    
    var $ = jQuery;
    
    if( confirm( "Are you sure you want to delete it?") ) {
        $( "#"+row_id ).remove();
    }
    return false;
}

/*
* Function to get the unique ID for the TOC row
*/
function get_uniqueid() {

    var uniqueid = Math.random().toString(16).slice(2);

    return uniqueid;
}