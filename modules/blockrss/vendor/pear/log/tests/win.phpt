--TEST--
Log: Window Handler
--INI--
date.timezone=UTC
--FILE--
<?php

require_once 'Log.php';

$conf = array('title' => 'Test Output');
$logger = Log::singleton('win', 'test', 'ident', $conf);

for ($i = 0; $i < 3; $i++) {
	$logger->log("Log entry $i");
}

--EXPECTF--
<script language="JavaScript">
test = window.open('', 'test', 'toolbar=no,scrollbars,width=600,height=400');
test.document.writeln('<html>');
test.document.writeln('<head>');
test.document.writeln('<title>Test Output</title>');
test.document.writeln('<style type="text/css">');
test.document.writeln('body { font-family: monospace; font-size: 8pt; }');
test.document.writeln('td,th { font-size: 8pt; }');
test.document.writeln('td,th { border-bottom: #999999 solid 1px; }');
test.document.writeln('td,th { border-right: #999999 solid 1px; }');
test.document.writeln('tr { text-align: left; vertical-align: top; }');
test.document.writeln('td.l0 { color: red; }');
test.document.writeln('td.l1 { color: orange; }');
test.document.writeln('td.l2 { color: yellow; }');
test.document.writeln('td.l3 { color: green; }');
test.document.writeln('td.l4 { color: blue; }');
test.document.writeln('td.l5 { color: indigo; }');
test.document.writeln('td.l6 { color: violet; }');
test.document.writeln('td.l7 { color: black; }');
test.document.writeln('</style>');
test.document.writeln('<script type="text/javascript">');
test.document.writeln('function scroll() {');
test.document.writeln(' body = document.getElementById("test");');
test.document.writeln(' body.scrollTop = body.scrollHeight;');
test.document.writeln('}');
test.document.writeln('<\/script>');
test.document.writeln('</head>');
test.document.writeln('<body id="test" onclick="scroll()">');
test.document.writeln('<table border="0" cellpadding="2" cellspacing="0">');
test.document.writeln('<tr><th>Time</th>');
test.document.writeln('<th>Ident</th>')
test.document.writeln('<th>Priority</th><th width="100%">Message</th></tr>');
</script><script language='JavaScript'>
test.document.writeln('<tr><td>%i:%i:%i.%i</td><td>ident</td><td>Info</td><td class=\"l6\">Log entry 0</td></tr>');
self.focus();
</script>
<script language='JavaScript'>
test.document.writeln('<tr><td>%i:%i:%i.%i</td><td>ident</td><td>Info</td><td class=\"l6\">Log entry 1</td></tr>');
self.focus();
</script>
<script language='JavaScript'>
test.document.writeln('<tr><td>%i:%i:%i.%i</td><td>ident</td><td>Info</td><td class=\"l6\">Log entry 2</td></tr>');
self.focus();
</script>
<script language='JavaScript'>
test.document.writeln('</table>');
self.focus();
</script>
<script language='JavaScript'>
test.document.writeln('</body></html>');
self.focus();
</script>
