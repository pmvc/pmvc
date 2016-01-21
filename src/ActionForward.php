<?php
/**
 * PMVC
 *
 * PHP version 5
 *
 * @category CategoryName
 * @package  PackageName
 * @author   Hill <hill@kimo.com>
 * @license  http://opensource.org/licenses/MIT MIT
 * @version  GIT: <git_id>
 * @link     https://packagist.org/packages/pmvc/pmvc
 */
namespace PMVC;

/**
 * PMVC ActionForward
 *
 * @category CategoryName
 * @package  PackageName
 * @author   Hill <hill@kimo.com>
 * @license  http://opensource.org/licenses/MIT MIT
 * @link     https://packagist.org/packages/pmvc/pmvc
 */
class ActionForward extends HashMap
{
    /**
     * Path
     *
     * @var string
     */
    private $_path;

    /**
     * Type
     *
     * @var string
     */
    private $_type;

    /**
     * Header
     *
     * @var array
     */
    private $_header=array();

    /**
     * LazyOutput action
     *
     * @var string
     */
    public $lazyOutput;

    /**
     * Default view engine
     *
     * @var object
     */
    public $view;

    /**
     * ActionForward
     *
     * @param array $forward forward
     */
    public function __construct($forward)
    {
        $this->setPath($forward[_PATH]);
        $this->setType($forward[_TYPE]);
        $this->setHeader($forward[_HEADER]);
        $this->lazyOutput = $forward[_LAZY_OUTPUT];
    }

    /**
     * Get header
     *
     * @return array header
     */
    public function getHeader()
    {
        return $this->_header;
    }

    /**
     * Set header
     *
     * @param array $v value
     *
     * @return mixed
     */
    public function setHeader($v)
    {
        return set($this->_header, $v);
    }

    /**
    * Set type
    *
    * @param string $type type
    *
    * @return void
    */
    public function setType($type=null)
    {
        if (is_null($type)) {
            $type='redirect';
        } elseif ('view'==$type) {
            $this->view=plug('view');
            $this->view['forward'] = $this;
        }
        $this->_type = $type;
    }

    /**
    * Get type
    *
    * @return string
    */
    public function getType()
    {
        return $this->_type;
    }

    /**
     * Get the path of the ActionForward.
     *
     * @param bool $bMerge merge or not
     *
     * @return string
     */
    public function getPath($bMerge=false)
    {
        $path = $this->_path;
        if ($bMerge) {
            $queryArray = $this->get();
            $path = $this->buildCommand(
                $path, 
                array(
                    'query' => $queryArray
                )
            );
        }
        return $path;
    }

    /**
     * Build URL from parse_url
     *
     * @param string $url    default url 
     * @param array  $params url overwrite params 
     *
     * @return string
     */
    public function buildCommand($url, $params)
    {
        $parsed_url = parse_url($url);
        if (!empty($params['query'])) {
            if (!empty($parsed_url['query'])) {
                parse_str($parsed_url['query'], $parsed_query);
                $parsed_url = array_merge($parsed_url, $params);
            } else {
                $parsed_query = array();
            }
            $parsed_url['query'] = http_build_query(
                array_merge(
                    $parsed_query,
                    $params['query']
                )
            );
        }
        $scheme   = isset($parsed_url['scheme']) ? $parsed_url['scheme'].'://' : ''; 
        $host     = isset($parsed_url['host']) ? $parsed_url['host'] : ''; 
        $port     = isset($parsed_url['port']) ? ':' . $parsed_url['port'] : ''; 
        $user     = isset($parsed_url['user']) ? $parsed_url['user'] : ''; 
        $pass     = isset($parsed_url['pass']) ? ':' . $parsed_url['pass']  : ''; 
        $pass     = ($user || $pass) ? "$pass@" : ''; 
        $path     = isset($parsed_url['path']) ? $parsed_url['path'] : ''; 
        $query    = isset($parsed_url['query']) ? '?' . $parsed_url['query'] : ''; 
        $fragment = isset($parsed_url['fragment'])?'#'. $parsed_url['fragment']: ''; 
        return "$scheme$user$pass$host$port$path$query$fragment"; 
    }

    /**
     * Set the path of the ActionForward.
     *
     * @param string $path path
     *
     * @return void
     */
    public function setPath($path)
    {
        $this->_path = $path;
    }

    /**
     * Set ActionFored key and value
     *
     * @param string $k key
     * @param string $v value
     *
     * @return bool
     */
    public function set($k, $v=null)
    {
        if ('view'==$this->getType()) {
            $args = func_get_args();
            return call_user_func_array(array($this->view,'set'), $args);
        } else {
            return $this[$k]=$v;
        }
    }

    /**
     * Get
     *
     * @param mixed $k       key
     * @param mixed $default default
     *
     * @return mixed
     */
    public function get($k=null, $default=null)
    {
        if ('view'==$this->getType()) {
            $args = func_get_args();
            $return = call_user_func_array(array($this->view,'get'), $args);
            return $return;
        } else {
            return get($this, $k, $default);
        }
    }

    /**
     * Process View
     *
     * @return $this
     */
    private function _processView()
    {
        call_plugin(
            'dispatcher',
            'notify',
            array(
                Event\B4_PROCESS_VIEW
                ,true
            )
        );
        $this->view->setThemeFolder(
            getOption(_TEMPLATE_DIR)
        );
        $this->view->setThemePath($this->getPath());
        $output = $this->view->process();
        if (!empty($this->lazyOutput)) {
            return $this;
        } else {
            return $output;
        }
    }

    /**
     * Execute ActionForward
     *
     * @return mixed
     */
    public function go()
    {
        switch ($this->getType()) {
        case 'view':
            return $this->_processView();
            break;
        case 'action':
            break;
        case 'redirect':
            $path = $this->getPath(true);
            header("Location: $path");
            break;
        default:
            break;
        }
    }
}
