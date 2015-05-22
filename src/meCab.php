<?php
namespace meCab;

/**
 * Class meCab
 * @package meCab
 */
class meCab{
    private $tmp_file;
    private $dictionary;
    private $dictionary_dir;

    function __construct()
    {
        $this->tmp_file = tempnam(sys_get_temp_dir(),'mecab');
        $this->dictionary_dir = $this->exec('echo `mecab-config --dicdir`',$res);
    }

    /**
     * @param $dictionary
     */
    public function setDictionary($dictionary){
        $path = $this->dictionary_dir.$dictionary;
        if(!file_exists($path)){
            throw new \Exception(sprintf('Not Found dictionary In "%s"',$dictionary));
        }
        $this->dictionary = $dictionary;
    }

    /**
     * @param $text
     * @return meCabWord[]|null
     * @throws \Exception
     */
    public function analysis($text){
        if(file_put_contents($this->tmp_file,$text)){
            $command = array('mecab');
            if($this->dictionary){
                $command[] = '-d '.$this->dictionary_dir.$this->dictionary;
            }
            $this->exec(implode(' ',$command).' '.$this->tmp_file,$res);
            if($res && (count($res) > 0)){
                if(preg_match('/ /',$res[0])){
                    throw new \Exception($res[0]);
                }
                $words = array();
                foreach($res as $k => $r){
                    if($r == 'EOS' && count($res) >= ($k + 1)){
                        break;
                    }
                    $words[] = new meCabWord($r);
                }
                return $words;
            }else{
                throw new \Exception(sprintf('Error text analysis.'));
            }
        }else{
            throw new \Exception(sprintf('Error write tmp file in %s',$this->tmp_file));
        }
    }

    /**
     * @param $command
     * @param $res
     * @return string
     */
    private function exec($command,&$res){
        if($text = exec($command,$res)){
        }
        return $text;
    }
}

/**
 * Class meCabWord
 * @package meCab
 */
class meCabWord{
    protected $str;
    protected $text;
    protected $speech;
    protected $speech_info;
    protected $conjugate;
    protected $conjugate_type;
    protected $original;
    protected $reading;
    protected $pronunciation;

    function __construct($text)
    {
        $this->str = $text;

        $res = preg_split('/\t/',$text);
        if(count($res) == 2){
            $this->text = $res[0];
            $info = explode(',',$res[1]);

            $this->speech_info = array_fill(0,3,null);
            foreach($info as $k => $t){
                if($t == '*'){
                    continue;
                }
                if($k == 0){
                    $this->speech = $t;
                }else if($k <= 3){
                    $this->speech_info[$k - 1] = $t;
                }else if($k == 4){
                    $this->conjugate = $t;
                }else if($k == 5){
                    $this->conjugate_type = $t;
                }else if($k == 6){
                    $this->original = $t;
                }else if($k == 7){
                    $this->reading = $t;
                }else if($k == 8){
                    $this->pronunciation = $t;
                }
            }
            $this->speech_info = array(

            );
        }
    }

    /**
     * @return mixed
     */
    public function getStr()
    {
        return $this->str;
    }

    /**
     * @return mixed
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * @return mixed
     */
    public function getSpeech()
    {
        return $this->speech;
    }

    /**
     * @return mixed
     */
    public function getSpeechInfo()
    {
        return $this->speech_info;
    }

    /**
     * @return mixed
     */
    public function getConjugate()
    {
        return $this->conjugate;
    }

    /**
     * @return mixed
     */
    public function getConjugateType()
    {
        return $this->conjugate_type;
    }

    /**
     * @return mixed
     */
    public function getPronunciation()
    {
        return $this->pronunciation;
    }

    /**
     * @return mixed
     */
    public function getReading()
    {
        return $this->reading;
    }

    /**
     * @return mixed
     */
    public function getOriginal()
    {
        return $this->original;
    }

}