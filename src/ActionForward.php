<?php
/**
 * PMVC.
 *
 * PHP version 5
 *
 * @category CategoryName
 *
 * @package PMVC
 *
 * @author  Hill <hill@kimo.com>
 * @license http://opensource.org/licenses/MIT MIT
 *
 * @version GIT: <git_id>
 *
 * @link https://packagist.org/packages/pmvc/pmvc
 */
namespace PMVC;

/**
 * PMVC ActionForward.
 *
 * @category CategoryName
 *
 * @package PMVC
 *
 * @author  Hill <hill@kimo.com>
 * @license http://opensource.org/licenses/MIT MIT
 *
 * @link https://packagist.org/packages/pmvc/pmvc
 */
class ActionForward extends HashMap
{
    /**
     * Path.
     *
     * @var string
     */
    private $_path;

    /**
     * Type.
     *
     * @var string
     */
    private $_type;

    /**
     * Header.
     *
     * @var array
     */
    private $_header = [];

    /**
     * Lazyoutput action.
     *
     * @var string
     */
    public $action;

    /**
     * ActionForward.
     *
     * @param array $forward forward
     */
    public function __construct($forward)
    {
        $this->setPath($forward[_PATH]);
        $this->_setType($forward[_TYPE]);
        $this->setHeader($forward[_HEADER]);
        $this->action = $forward[_ACTION];
    }

    /**
     * Set header.
     *
     * @param array $v value
     *
     * @return mixed
     */
    public function cleanHeader($v)
    {
        return clean($this->_header, $v);
    }

    /**
     * Get header.
     *
     * @return array header
     */
    public function &getHeader()
    {
        return $this->_header;
    }

    /**
     * Set header.
     *
     * @param array $v value
     *
     * @return mixed
     */
    public function setHeader($v)
    {
        if (empty($v)) {
            return;
        }

        return set($this->_header, toArray($v));
    }

    /**
     * Set type.
     *
     * @param string $type type
     *
     * @return void
     */
    private function _setType($type = null)
    {
        if (is_null($type)) {
            $type = 'redirect';
        }
        $this->_type = $type;
    }

    /**
     * Get type.
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
    public function getPath($bMerge = false)
    {
        $path = $this->_path;
        if ($bMerge) {
            $path = $this->buildCommand(
                $path,
                $this->get()
            );
        }

        return $path;
    }

    /**
     * Build URL from parse_url.
     *
     * @param string $url    default url
     * @param array  $params url overwrite params
     *
     * @return string
     */
    public function buildCommand($url, $params)
    {
        return callPlugin(
            getOption(_ROUTER),
            __FUNCTION__,
            [
                $url,
                $params,
            ]
        );
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
     * Set ActionFored key and value.
     *
     * @param string $k key
     * @param string $v value
     *
     * @return bool
     */
    public function set($k, $v = null)
    {
        return set($this, $k, $v);
    }

    /**
     * Get.
     *
     * @param mixed $k       key
     * @param mixed $default default
     *
     * @return mixed
     */
    public function get($k = null, $default = null)
    {
        return get($this, $k, $default);
    }

    /**
     * Process Header.
     *
     * @return $this
     */
    private function _processHeader()
    {
        $headers = $this->getHeader();

        return callPlugin(
            getOption(_ROUTER),
            'processHeader',
            [
                $this->getHeader(),
            ]
        );
    }

    /**
     * Process View.
     *
     * @return $this
     */
    private function _processView()
    {
        callPlugin(
            'dispatcher',
            'notify',
            [
                Event\B4_PROCESS_VIEW, true,
            ]
        );
        
        $view = plug('view');
        $view['forward'] = $this;
        $view->setThemeFolder(
            getOption(_TEMPLATE_DIR)
        );
        $view->setThemePath($this->getPath());
        if (isset($view['headers'])) {
            $this->setHeader($view['headers']);
        }
        $this->_processHeader();
        $view->set(get($this));
        return $view->process();
    }

    /**
     * Execute ActionForward.
     *
     * @return mixed
     */
    public function go()
    {
        switch ($this->getType()) {
        case 'view':
            return $this->_processView();
        case 'redirect':
            $this->_processHeader();
            $path = $this->getPath(true);

            return callPlugin(
                getOption(_ROUTER),
                'go',
                [
                    $path,
                ]
            );
        case 'action':
        default:
            return $this;
        }
    }
}
