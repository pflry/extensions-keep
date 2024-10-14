<div class="wrap ekeep">
  <h1 class="headline-h1"><?php echo esc_html__('Extensions Keep', 'extensions-keep'); ?></h1>
  <div class="ekeep-export">
    <h2 class="headline-h2"><?php echo EXTENSIONS_KEEP_Icons::get_svg('file-arrow-down'); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><?php echo esc_html__('Exporter la liste d\'extensions', 'extensions-keep'); ?></h2>
    <p><?php echo esc_html__('En cliquant sur le bouton &laquo; Exporter &raquo; vous allez générer une liste de toutes les extensions installées et actives.', 'extensions-keep'); ?><br><?php echo esc_html__('Pour ignorer une extension, il faut la désactiver.', 'extensions-keep'); ?><br>
    <?php echo esc_html__('Cette liste va être exportée dans un fichier au format JSON.', 'extensions-keep'); ?></p>
    <?php EKEEP_Admin::render_export_form(); ?>
  </div>
  <hr>
  <div class="ekeep-upload">
    <h2 class="headline-h2"><?php echo EXTENSIONS_KEEP_Icons::get_svg('file-arrow-up'); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><?php echo esc_html__('Importer et installer une liste d\'extensions', 'extensions-keep'); ?></h2>
    <p><?php echo esc_html__('Choisir un fichier JSON généré par Extensions Keep.', 'extensions-keep'); ?><br><?php echo esc_html__('En cliquant sur le bouton &laquo; Importer &raquo; vous lancez l\'installation de toutes les extensions présentes dans le fichier.', 'extensions-keep'); ?><br><?php echo esc_html__('Les extensions importées seront ajoutées et fusionnées avec celles déjà installées.', 'extensions-keep'); ?></p>
    <?php EKEEP_Admin::render_upload_form(); ?>
  </div>
  <hr>
  <h2 class="headline-h2"><?php echo EXTENSIONS_KEEP_Icons::get_svg('notepad');  // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><?php echo esc_html__('Journal des actions', 'extensions-keep'); ?></h2>
  <?php
  $this->logger->display_log();
  ?>
</div>