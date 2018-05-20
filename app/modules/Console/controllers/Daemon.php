<?php
/*
 * PHP Daemon sample.
 *
 * @see http://www.netkiller.cn/journal/php.daemon.html
*/

final class Signal
{
    public static $signo = 0;
    protected static $ini = null;

    public static function set($signo)
    {
        self::$signo = $signo;
    }

    public static function get()
    {
        return(self::$signo);
    }

    public static function reset()
    {
        self::$signo = 0;
    }
}

class Test {
    //public static $signal = null;

    public function __construct() {
        //self::$signal == null;
    }
    public function run(){
        //while(true){
            //pcntl_signal_dispatch();
            printf(".");
            //sleep(1);
            if(Signal::get() == SIGHUP){
                Signal::reset();
                //break;
            }
        //}
        printf("\n");
    }
}

class Daemon {
    /* config */
    const uid = 80;
    const gid = 80;
    const sleep	= 5;

    protected $pool 	= NULL;
    protected $config	= array();
    protected $gid;
    protected $uid;
    protected $pidFile;
    protected $class;
    protected $action;

    public function __construct($class, $action = 'run')
    {
        $this->pidFile = '/var/run/yaf-'.basename(get_class($class), '.php').'.pid';
        //$this->config = parse_ini_file('sender.ini', true);
        $this->class = $class;
        $this->action = $action;

        $this->signal();
    }

    public function signal()
    {
        pcntl_signal(SIGHUP,  function($signo) /*use ()*/{
            //echo "\n This signal is called. [$signo] \n";
            printf("The process has been reload.\n");
            Signal::set($signo);
        });

    }

    private function daemon()
    {
        if (file_exists($this->pidFile)) {
            printf("The file $this->pidFile exists.\n");
            exit();
        }

        $pid = pcntl_fork();
        if ($pid == -1) {
            die('could not fork');
        } else if ($pid) {
            // we are the parent
            //pcntl_wait($status); //Protect against Zombie children
            exit($pid);
        } else {
            file_put_contents($this->pidFile, getmypid());
            posix_setuid(self::uid);
            posix_setgid(self::gid);
            return(getmypid());
        }
    }

    private function run()
    {
        while(true){
            printf("The process begin.\n");
            $action = $this->action;
            $this->class->$action();
            printf("The process end.\n");
        }
    }

    private function foreground()
    {
        $this->run();
    }

    private function start()
    {
        $pid = $this->daemon();
        printf("The process pid is : \n", $pid);

        for(;;){
            $this->run();
            sleep(self::sleep);
        }
    }

    private function stop()
    {
        if (file_exists($this->pidFile)) {
            $pid = file_get_contents($this->pidFile);
            posix_kill($pid, 9);
            unlink($this->pidFile);
        }
    }

    private function reload()
    {
        if (file_exists($this->pidFile)) {
            $pid = file_get_contents($this->pidFile);
            //posix_kill(posix_getpid(), SIGHUP);
            posix_kill($pid, SIGHUP);
        }
    }

    private function status()
    {
        if (file_exists($this->pidFile)) {
            $pid = file_get_contents($this->pidFile);
            system(sprintf("ps ax | grep %s | grep -v grep", $pid));
        }
    }

    private function help($proc){
        printf("%s start | stop | restart | status | foreground | help \n", $proc);
    }

    public function main($argv)
    {
        if(count($argv) < 2){
            $this->help($argv[0]);
            printf("please input help parameter\n");
            exit();
        }

        if($argv[1] === 'stop'){
            $this->stop();
        }else if($argv[1] === 'start'){
            $this->start();
        }else if($argv[1] === 'restart'){
            $this->stop();
            $this->start();
        }else if($argv[1] === 'status'){
            $this->status();
        }else if($argv[1] === 'foreground'){
            $this->foreground();
        }else if($argv[1] === 'reload'){
            $this->reload();
        }else{
            $this->help($argv[0]);
        }
    }
}

$daemon = new Daemon(new Test());
$daemon->main($argv);
?>