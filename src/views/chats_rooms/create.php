<div class="wrap">
    <h1 class="mb-4"><?php _e('Create AI Interview', 'wbe-plugin'); ?></h1>

    <form id="create_ai_interview_form" method="post" class="nice-form-group">

        <div class="nice-form-group">
            <label for="meeting_link"><?php _e('Meeting Link', 'wbe-plugin'); ?></label>
            <input type="url" name="meeting_link" id="meeting_link"/>
        </div>

        <div class="nice-form-group">
            <label for="type"><?php _e('Type', 'wbe-plugin'); ?></label>
            <select name="type" id="type">
                <option value="other"><?php _e('Other', 'wbe-plugin'); ?></option>
                <option value="google_meet"><?php _e('Google Meet', 'wbe-plugin'); ?></option>
                <option value="zoom"><?php _e('Zoom', 'wbe-plugin'); ?></option>
                <option value="microsoft_teams"><?php _e('Microsoft Teams', 'wbe-plugin'); ?></option>
                <option value="skype"><?php _e('Skype', 'wbe-plugin'); ?></option>
                <option value="discord"><?php _e('Discord', 'wbe-plugin'); ?></option>
                <option value="slack_huddle"><?php _e('Slack Huddle', 'wbe-plugin'); ?></option>
                <option value="webex"><?php _e('Webex', 'wbe-plugin'); ?></option>
                <option value="jitsi"><?php _e('Jitsi Meet', 'wbe-plugin'); ?></option>
                <option value="whereby"><?php _e('Whereby', 'wbe-plugin'); ?></option>
            </select>
        </div>

        <div class="nice-form-group">
            <label for="details"><?php _e('Details', 'wbe-plugin'); ?></label>
            <textarea name="details" id="details"></textarea>
        </div>

        <div class="nice-form-group">
            <label for="attachments"><?php _e('CV', 'wbe-plugin'); ?></label>
              <input type="file" id="attachments" name="attachments" accept=".json"/>
        </div>

        <div class="nice-form-group">
            <label for="time"><?php _e('Time (seconds)', 'wbe-plugin'); ?></label>
            <select name="time" id="time">
                <option value="1800"><?php _e('30 minutes', 'wbe-plugin'); ?></option>
                <option value="3600"><?php _e('1 hour', 'wbe-plugin'); ?></option>
                <option value="5400"><?php _e('1 hour 30 minutes', 'wbe-plugin'); ?></option>
                <option value="7200"><?php _e('2 hours', 'wbe-plugin'); ?></option>
            </select>
        </div>

        <div class="nice-form-group">
            <label>
                <input type="checkbox" name="interview_complete" value="1">
                <?php _e('Interview complete?', 'wbe-plugin'); ?>
            </label>
        </div>

        <div class="nice-form-group">
            <label>
                <input type="checkbox" name="in_use" value="1">
                <?php _e('Currently in use?', 'wbe-plugin'); ?>
            </label>
        </div>

        <input type="hidden" name="action" value="create_ai_interview">
        <?php wp_nonce_field('create_ai_interview', 'ai_interview_nonce'); ?>

        <div class="nice-form-group">
            <button type="submit"><?php _e('Create Interview', 'wbe-plugin'); ?></button>
        </div>
    </form>
</div>

