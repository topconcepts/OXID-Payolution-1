<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
  <title>[{ $title }]</title>
  <meta http-equiv="Content-Type" content="text/html; charset=[{$charset}]">
  <link rel="stylesheet" href="[{$oViewConf->getResourceUrl()}]main.css">
</head>
<body>
  <form name="transfer" id="transfer" action="[{ $oViewConf->getSelfLink() }]" method="post">
    [{ $oViewConf->getHiddenSid() }]
    <input type="hidden" name="oxid" value="1">
    <input type="hidden" name="cl" value="">
  </form>
  <br>
  [{include file="inc_error.tpl" Errorlist=$Errors.default}]
</body>
</html>