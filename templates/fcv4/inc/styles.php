<?php
defined('_JEXEC') or die('Restricted access');

$css_path = (JDEBUG) ? '/media/fc/css/styles.css' : '/media/fc/assets/css/20160524143029.styles.min.css';
?>
<script id="loadcss">
    (function (w) {
      "use strict";
      var loadCSS = function (href, before, media) {
        var doc = w.document;
        var ss = doc.createElement("link");
        var ref;
        if (before) {
          ref = before;
        }
        else {
          var refs = (doc.body || doc.getElementsByTagName("head")[ 0 ]).childNodes;
          ref = refs[ refs.length - 1];
        }

        var sheets = doc.styleSheets;
        ss.rel = "stylesheet";
        ss.href = href;
        ss.media = "only x";

        ref.parentNode.insertBefore(ss, (before ? ref : ref.nextSibling));
        var onloadcssdefined = function (cb) {
          var resolvedHref = ss.href;
          var i = sheets.length;
          while (i--) {
            if (sheets[ i ].href === resolvedHref) {
              return cb();
            }
          }
          setTimeout(function () {
            onloadcssdefined(cb);
          });
        };
        ss.onloadcssdefined = onloadcssdefined;
        onloadcssdefined(function () {
          ss.media = media || "all";
        });
        return ss;
      };
      // commonjs
      if (typeof module !== "undefined") {
        module.exports = loadCSS;
      }
      else {
        w.loadCSS = loadCSS;
      }
    }(typeof global !== "undefined" ? global : this));
    loadCSS(<?php echo '\'' . $css_path . '\'' ?>, document.getElementById("loadcss"));
</script>
<noscript><link href="<?php echo '\'' . $css_path . '\'' ?>" rel="stylesheet"></noscript>