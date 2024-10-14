(function($) {
  $(document).ready(function () {
    $('#ekeep-export-form').on('submit', function() {
      setTimeout(function() {
        location.reload();
      }, 1000); // Recharge la page après 1 seconde
    });

    var $fileInput = $('input[name="plugins_list_file"]');
    var $submitButton = $fileInput.closest('form').find('input[type="submit"]');

    // Désactiver le bouton au chargement de la page
    $submitButton.prop('disabled', true);

    // Activer/désactiver le bouton en fonction de la sélection de fichier
    $fileInput.on('change', function() {
      $submitButton.prop('disabled', !$(this).val());
    });

    // Déclencher le téléchargement si nécessaire
    if (typeof ekeep_vars !== 'undefined' && ekeep_vars.download_filename) {
      var downloadUrl = ekeep_vars.download_url + '&ekeep_download=' + encodeURIComponent(ekeep_vars.download_filename);
      window.location.href = downloadUrl;
    }
  });
})(jQuery);