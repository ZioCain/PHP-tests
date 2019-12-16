<?php
class IOMode
{
    public $stdin;
    public $stdout;
    public $stderr;

    private function getMode(&$dev, $fp)
    {
        $stat = fstat($fp);
        $mode = $stat['mode'] & 0170000; // S_IFMT

        $dev = new StdClass;

        $dev->isFifo = $mode == 0010000; // S_IFIFO
        $dev->isChr  = $mode == 0020000; // S_IFCHR
        $dev->isDir  = $mode == 0040000; // S_IFDIR
        $dev->isBlk  = $mode == 0060000; // S_IFBLK
        $dev->isReg  = $mode == 0100000; // S_IFREG
        $dev->isLnk  = $mode == 0120000; // S_IFLNK
        $dev->isSock = $mode == 0140000; // S_IFSOCK
    }

    public function __construct()
    {
        $this->getMode($this->stdin,  STDIN);
        $this->getMode($this->stdout, STDOUT);
        $this->getMode($this->stderr, STDERR);
    }
}

$io = new IOMode;

// taken from https://stackoverflow.com/questions/11327367/detect-if-a-php-script-is-being-run-interactively-or-not/11327451
