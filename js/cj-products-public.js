jQuery(document).ready(function($) {
    $('.samplize-button').on('click', function(event) {
        event.preventDefault();
        var sampleUrl = $(this).data('url');
        var sampleHex = $(this).data('hex');
        var sampleName = $(this).data('name');
        var sampleId = $(this).data('identifier');
        var popupText1 = cjProductsSettings.popupText1 || "Don't forget to get";
        var popupText2 = cjProductsSettings.popupText2 || "2 FREE SAMPLES!";
        var buttonText = cjProductsSettings.popupButtonText || "CHECK A SAMPLE!";

        var popupContent = `
      <div class="samplize-popup">
        <div class="samplize-popup-content">
          <div class="samplize-popup-header">
            <p>${popupText1}</p>
            <p>${popupText2}</p>
          </div>
          <div class="samplize-popup-body">
            <div class="block-paint" style="background: #${sampleHex}"></div>
            <p>${sampleName}</p>
            <p>${sampleId}</p>
          </div>
          <div class="samplize-popup-footer">
            <a class="samplize-popup-link" href="${sampleUrl}" target="_blank">${buttonText}</a>
          </div>
          <button class="samplize-popup-close">×</button>
        </div>
        
      </div>
    `;

        $('body').append(popupContent);
    });

    $(document).on('click', '.samplize-popup-close', function() {
        $('.samplize-popup').remove();
    });
});