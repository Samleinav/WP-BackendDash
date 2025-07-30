<div class="wrap">
    <h1 class="mb-4"><?php _e('Update AI Interview', 'wbe-plugin'); ?></h1>

    <form id="create_ai_interview_form" action="<?= wberouteapi('chat.update',['token' => $roomChat->token]) ?>" method="post" class="nice-form-group wbe-form" enctype="multipart/form-data">

        <div class="nice-form-group">
            <label for="meeting_link"><?php _e('Meeting Link', 'wbe-plugin'); ?></label>
            <input type="url" name="meeting_link" id="meeting_link" value="<?= esc_attr($roomChat->meeting_link ?? '') ?>"/>
        </div>

        <div class="nice-form-group">
            <label for="type"><?php _e('Type', 'wbe-plugin'); ?></label>
            <select name="type" id="type">
                <?php
                $types = [
                    'other' => __('Other', 'wbe-plugin'),
                    'google_meet' => __('Google Meet', 'wbe-plugin'),
                    'zoom' => __('Zoom', 'wbe-plugin'),
                    'microsoft_teams' => __('Microsoft Teams', 'wbe-plugin'),
                    'skype' => __('Skype', 'wbe-plugin'),
                    'discord' => __('Discord', 'wbe-plugin'),
                    'slack_huddle' => __('Slack Huddle', 'wbe-plugin'),
                    'webex' => __('Webex', 'wbe-plugin'),
                    'jitsi' => __('Jitsi Meet', 'wbe-plugin'),
                    'whereby' => __('Whereby', 'wbe-plugin'),
                ];
                foreach ($types as $value => $label) {
                    $selected = ($roomChat->type ?? '') === $value ? 'selected' : '';
                    echo "<option value=\"$value\" $selected>$label</option>";
                }
                ?>
            </select>
        </div>

        <div class="nice-form-group">
            <label for="details"><?php _e('Details', 'wbe-plugin'); ?></label>
            <textarea name="details" id="details"><?= esc_textarea($roomChat->details ?? '') ?></textarea>
        </div>

        <div class="nice-form-group">
            <label for="attachments"><?php _e('CV', 'wbe-plugin'); ?></label>
            <input type="file" id="attachments" name="attachments" accept=".json"/>
            <?php if (!empty($roomChat->attachments)): ?>
                <p><?= basename( get_attached_file( $attachment_id ) ); ?></p>
            <?php endif; ?>
        </div>

        <div class="nice-form-group">
            <label>
                <input type="checkbox" name="interview_complete" value="1" <?= $roomChat->interview_complete ? 'checked' : '' ?>>
                <?php _e('Interview complete?', 'wbe-plugin'); ?>
            </label>
        </div>

        <div class="nice-form-group">
            <label>
                <input type="checkbox" name="in_use" value="1" <?= $roomChat->in_use ? 'checked' : '' ?>>
                <?php _e('Currently in use?', 'wbe-plugin'); ?>
            </label>
        </div>

        <input type="hidden" name="action" value="create_ai_interview">
        <?php wp_nonce_field('create_ai_interview', 'ai_interview_nonce'); ?>

        <div class="nice-form-group">
            <button type="submit"><?php _e('Update Interview', 'wbe-plugin'); ?></button>
        </div>
    </form>
</div>

