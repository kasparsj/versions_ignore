<?php

function matchFile($file, $ignoreFile) {
    $lines = file($ignoreFile);
    foreach ($lines as $line) {
        if (matchLine($line, $file)) {
            return true;
        }
    }
    return false;
}

function matchLine($line, $dir, $file) {
    $hash = strpos($line, '#');
    if ($hash !== false) {
        $line = substr($line, $hash);
    }
    $line = trim($line);
    if ($line === '') return false;
    $negate = substr($line, 0, 1) == '!';
    if ($negate) {
        $line = substr($line, 1);
    }
    $useglob = substr($line, 0, 2) == '**';
    $expr = preg_replace(['/(\.)?(\*)+/', '/(\.)?(\+)+/'], ['.*', '.+'], $line);
    if ($useglob) {
        if ($negate) {
            $matches = array_diff(glob("$dir/*"), glob("$dir/$line"));
        } else {
            $matches = glob("$dir/$line");
        }
        foreach ($matches as $match) {
            if (preg_match('|^' . preg_quote($dir, '|') . '/(.+/)*'  . $expr . '|', $match)) {
                return true;
            }
        }
    }
    else {
        if (preg_match('|^' . preg_quote($dir, '|') . '/(.+/)*'  . $expr . '|', $file) && !$negate) {
            return true;
        }
    }
    return false;
}

assert(matchLine('test.php', '/home/kasparsj/Nextcloud/Work/versions_ignore/tests/files', '/home/kasparsj/Nextcloud/Work/versions_ignore/tests/files/file.php') == false);
assert(matchLine('test.php', '/home/kasparsj/Nextcloud/Work/versions_ignore/tests', '/home/kasparsj/Nextcloud/Work/versions_ignore/tests/files/file.php') == false);
assert(matchLine('#test.php', '/home/kasparsj/Nextcloud/Work/versions_ignore/tests/files', '/home/kasparsj/Nextcloud/Work/versions_ignore/tests/files/test.php') == false);
assert(matchLine('#test.php', '/home/kasparsj/Nextcloud/Work/versions_ignore/tests', '/home/kasparsj/Nextcloud/Work/versions_ignore/tests/files/test.php') == false);
assert(matchLine('!test.php', '/home/kasparsj/Nextcloud/Work/versions_ignore/tests/files', '/home/kasparsj/Nextcloud/Work/versions_ignore/tests/files/test.php') == false);
assert(matchLine('!test.php', '/home/kasparsj/Nextcloud/Work/versions_ignore/tests', '/home/kasparsj/Nextcloud/Work/versions_ignore/tests/files/test.php') == false);
assert(matchLine('**.git', '/home/kasparsj/Nextcloud/Work/versions_ignore/tests/files', '/home/kasparsj/Nextcloud/Work/versions_ignore/tests/files/test.jpg') == false);
assert(matchLine('**.git', '/home/kasparsj/Nextcloud/Work/versions_ignore/tests', '/home/kasparsj/Nextcloud/Work/versions_ignore/tests/files/test.jpg') == false);

assert(matchLine('test.php', '/home/kasparsj/Nextcloud/Work/versions_ignore/tests/files', '/home/kasparsj/Nextcloud/Work/versions_ignore/tests/files/test.php') == true);
assert(matchLine('test.php', '/home/kasparsj/Nextcloud/Work/versions_ignore/tests', '/home/kasparsj/Nextcloud/Work/versions_ignore/tests/files/test.php') == true);
assert(matchLine('test.*', '/home/kasparsj/Nextcloud/Work/versions_ignore/tests/files', '/home/kasparsj/Nextcloud/Work/versions_ignore/tests/files/test.jpg') == true);
assert(matchLine('test.*', '/home/kasparsj/Nextcloud/Work/versions_ignore/tests', '/home/kasparsj/Nextcloud/Work/versions_ignore/tests/files/test.jpg') == true);
assert(matchLine('*.php', '/home/kasparsj/Nextcloud/Work/versions_ignore/tests/files', '/home/kasparsj/Nextcloud/Work/versions_ignore/tests/files/file.php') == true);
assert(matchLine('*.php', '/home/kasparsj/Nextcloud/Work/versions_ignore/tests', '/home/kasparsj/Nextcloud/Work/versions_ignore/tests/files/file.php') == true);
assert(matchLine('*.php', '/home/kasparsj/Nextcloud/Work/versions_ignore/tests/files', '/home/kasparsj/Nextcloud/Work/versions_ignore/tests/files/.php/test') == true);
assert(matchLine('*.php', '/home/kasparsj/Nextcloud/Work/versions_ignore/tests', '/home/kasparsj/Nextcloud/Work/versions_ignore/tests/files/.php/test') == true);
assert(matchLine('*', '/home/kasparsj/Nextcloud/Work/versions_ignore/tests/files', '/home/kasparsj/Nextcloud/Work/versions_ignore/tests/files/test.php') == true);
assert(matchLine('*', '/home/kasparsj/Nextcloud/Work/versions_ignore/tests', '/home/kasparsj/Nextcloud/Work/versions_ignore/tests/files/test.php') == true);
assert(matchLine('*', '/home/kasparsj/Nextcloud/Work/versions_ignore/tests/files', '/home/kasparsj/Nextcloud/Work/versions_ignore/tests/files/test.jpg') == true);
assert(matchLine('*', '/home/kasparsj/Nextcloud/Work/versions_ignore/tests', '/home/kasparsj/Nextcloud/Work/versions_ignore/tests/files/test.jpg') == true);
assert(matchLine('**.php', '/home/kasparsj/Nextcloud/Work/versions_ignore/tests/files', '/home/kasparsj/Nextcloud/Work/versions_ignore/tests/files/test.jpg') == true);
assert(matchLine('**.php', '/home/kasparsj/Nextcloud/Work/versions_ignore/tests', '/home/kasparsj/Nextcloud/Work/versions_ignore/tests/files/test.jpg') == true);
assert(matchLine('!**.php', '/home/kasparsj/Nextcloud/Work/versions_ignore/tests/files', '/home/kasparsj/Nextcloud/Work/versions_ignore/tests/files/test.jpg') == false);
assert(matchLine('!**.php', '/home/kasparsj/Nextcloud/Work/versions_ignore/tests', '/home/kasparsj/Nextcloud/Work/versions_ignore/tests/files/test.jpg') == false);