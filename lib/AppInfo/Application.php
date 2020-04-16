<?php

declare(strict_types=1);

namespace OCA\Versions_Ignore\AppInfo;

use OCP\AppFramework\App;
use OCA\Files_Versions\Capabilities;
use OCP\EventDispatcher\IEventDispatcher;
use OCA\Files_Versions\Events\CreateVersionEvent;

class Application extends App {

    const APP_ID = 'versions_ignore';

    public function __construct(array $urlParams = []) {
        parent::__construct(self::APP_ID, $urlParams);

        $container = $this->getContainer();
        $container->registerCapability(Capabilities::class);
    }

    public function registerEvents(): void {
        $eventDispatcher = $this->getContainer()->getServer()->getEventDispatcher();
        $eventDispatcher->addListener('OCA\Files_Versions::createVersion', function(CreateVersionEvent $event) {
            $node = $event->getNode();
            $parent = $node;
            do {
                $parent = $parent->getParent();
                foreach (['.versionsignore', '.versions_ignore'] as $ignoreFileName) {
                    if ($parent->nodeExists($ignoreFileName)) {
                        if ($this->matchFile($node->getPath(), $parent->get($ignoreFileName))) {
                            $event->disableVersions();
                            return;
                        }
                    }
                }
            } while (!($parent instanceof IRootFolder));
        });
    }

    private function matchFile(string $file, string $ignoreFile): bool {
        $lines = file($ignoreFile);
        $dir = dirname($ignoreFile);
        foreach ($lines as $line) {
            if ($this->matchLine($line, $dir, $file)) {
                return true;
            }
        }
        return false;
    }

    private function matchLine(string $line, string $dir, string $file): bool {
        $line = trim($line);
        if ($line === '') return false;                 # empty line
        if (substr($line, 0, 1) == '#') return false;   # a comment
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
}
