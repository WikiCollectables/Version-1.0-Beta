<?php

/**
 * wcImageView.php
 *
 * Image viewing page shown in popup window.
 * 
 * Application: wikicoins.com buy/sell feature
 *
 * @package    wcImageView.php
 * @author     Thomas Knierim
 * @copyright  (c) 2006 wikicoins.com
 * @version    1.0
 * @see        wcBuySellPage.php
 *
 */

require_once('extensions/buysell/model/wcConfig.php');

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
  <head>
    <title>Detail View of item no. <? echo htmlentities($itemNo, ENT_COMPAT, 'UTF-8'); ?>
    </title>
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    <style type="text/css">
      html, body { height: 100%; overflow: auto; color: #000000; background-color: #FFFFFF; margin: 0px; padding: 0px; }
      table { border-collapse: collapse;  border-spacing: 0; }
      td, th { padding: 0; }
    </style>
    <script type="text/javascript" language="JavaScript">
      function loadImage (imageLink) { 
        document.getElementById('image').src = imageLink.href; 
        return false;
      }
      function fitWindow() {
        tbl = document.getElementById("wrappertable");
        iWidth = tbl.clientWidth + 50;
        iHeight = tbl.clientHeight + 60;
        window.resizeTo(iWidth, iHeight);
      }
    </script>
  </head>
  <body>
  <table id="wrappertable" style="margin-left: 20px; margin-top: 20px">
    <tr>
      <td style="vertical-align: top;">
      <table>
        <?
          $firstImage = '';
          $fileExtensions = array('.jpg', '.gif', '.png');
          for ($i = 1; $i <=5; $i++) {
              $image = $itemNo . '-' . $i;
              $thumbnailFile = wcConfig::IMAGE_UPLOAD_DIR . '/' . $image . '-tn.jpg';
              $imageURL = '';
              if (file_exists($thumbnailFile)) {
                  $thumbnailURL = wcConfig::IMAGE_URL . '/' . $image . '-tn.jpg';
                  foreach ($fileExtensions as $ext) {
                      $imageFile = wcConfig::IMAGE_UPLOAD_DIR . '/' . $image . $ext;
                      if (file_exists($imageFile)) {
                          $imageURL = wcConfig::IMAGE_URL . '/' . $image . $ext;
                          if ($i == 1)
                              $firstImage = $imageURL;
                      }
                  }
                  echo '<tr><td style="vertical-align: top;  text-align: center; padding-bottom: 10px">';
                  if (!empty($imageURL))
                    echo '<a href="', $imageURL, '" onclick="return loadImage(this)">';
                  echo '<img src="', $thumbnailURL, '" border="0" alt="Image" />';
                  if (!empty($imageURL))
                    echo '</a>';
                  echo '</td></tr>', "\n";
              }
          }
        ?>
      </table>
      </td>
      <td style="width: 10px;"></td>
      <td style="width: 1px; background-color: #999999;">
      <td style="width: 10px;"></td>
      <td style="vertical-align: top;">
        <?
          echo '<img src="', $firstImage, '" border="0" alt="Image" id="image" />';
        ?>
      </td>
    </tr>
    <tr>
    <td colspan="5" style="height: 40px; vertical-align: bottom; text-align: center;">
      <form action="#">
        <input type="button" value="Close" onclick="window.close();" />
      </form>
    </td>
    </tr>
  </table>
  </body>
</html>
