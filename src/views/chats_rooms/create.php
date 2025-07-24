<?php

?>
<div class="wrap">
    <h1 class="mb-4">Crear Entrevista AI</h1>
    <form id="crear_entrevista_form" method="post" class="container">
        <div class="row mb-3">
            <label class="form-label col-sm-2" for="user_id">Usuario ID</label>
            <div class="col-sm-10">
                <input type="number" name="user_id" class="form-control" required />
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label" for="meeting_link">Enlace de reunión</label>
            <textarea name="meeting_link" class="form-control"></textarea>
        </div>

        <div class="mb-3">
            <label class="form-label" for="type">Tipo</label>
            <input name="type" type="text" class="form-control" />
        </div>

        <div class="mb-3">
            <label class="form-label" for="details">Detalles</label>
            <textarea name="details" class="form-control"></textarea>
        </div>

        <div class="mb-3">
            <label class="form-label" for="attachments">Adjuntos (JSON)</label>
            <textarea name="attachments" class="form-control"></textarea>
        </div>

        <div class="row mb-3">
            <div class="col">
                <label class="form-label" for="time">Tiempo (seg)</label>
                <input name="time" type="number" step="0.01" class="form-control" />
            </div>
            <div class="col">
                <label class="form-label" for="tokens">Tokens</label>
                <input name="tokens" type="number" class="form-control" />
            </div>
        </div>

        <div class="form-check mb-3">
            <input name="interview_complete" type="checkbox" class="form-check-input" value="1" id="complete" />
            <label class="form-check-label" for="complete">¿Entrevista completa?</label>
        </div>

        <div class="form-check mb-3">
            <input name="in_use" type="checkbox" class="form-check-input" value="1" id="in_use" />
            <label class="form-check-label" for="in_use">¿En uso?</label>
        </div>

        <input type="hidden" name="action" value="crear_ai_entrevista" />
        <?php wp_nonce_field('crear_ai_entrevista', 'ai_entrevista_nonce'); ?>

        <button type="submit" class="btn btn-primary">Crear Entrevista</button>
    </form>
</div>
