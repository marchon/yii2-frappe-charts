<?php
/**
 * @Author: Hzhihua
 * @Time: 17-12-12 上午11:14
 * @Email: cnzhihua@gmail.com
 */

namespace hzhihua\frappe;

use yii\base\Exception;
use yii\base\Widget;

/**
 * 生成图形统计图
 * ```php
 * <?= Charts::widget([
 *     'config' => [
 *         'parent' => '#chart',
 *         'height' => 250,
 *         'title' => "My Chart",
 *         'type' => 'bar',
 *         'colors' => ['#7cd6fd', '#743ee2'],
 *         'format_tooltip_x' => '<%d => (d +  * \'\').toUpperCase()%>',
 *     ],
 *     'data' => [
 *         'labels' => ["12am-3am", "3am-6pm",  * "6am-9am", "9am-12am", "12pm-3pm", "3pm-6pm",  * "6pm-9pm", "9am-12am"
 *         ],
 *         'datasets' => [
 *             [
 *                 'title' => "Some Data",
 *                 'values' => [25, 40, 30, 35, 8, 52,  * 17, -4],
 *             ],
 *             [
 *                 'title' => "Another Set",
 *                 'values' => [25, 50, -10, 15, 18, 32,  * 27, 14],
 *             ],
 *         ],
 *     ],
 *     'other' => <<<'JS'
 * chart.show_sums();
 * chart.show_averages();
 * JS
 * ]);
 * ?>
 * ```
 *
 * @package hzhihua\frappe
 * @property \yii\web\View $view
 * @see rappe-charts https://github.com/frappe/charts
 * @see rappe-charts-demo https://frappe.github.io/charts/
 * @see github https://github.com/Hzhihua/rappe-charts
 */
class Charts extends Widget
{
    /**
     * 数据
     * @var array
     */
    public $data = [];

    /**
     * 配置参数
     * @var array
     */
    public $config = [];

    /**
     * 额外的函数调用
     * ```php
     * 'other' => <<<'JS'
     * chart.show_sums();
     * chart.show_averages();
     * JS
     * ```
     *
     * ```js
     * chart.show_sums();  // 求总和 and `hide_sums()`
     * chart.show_averages();  // 求平均值 and `hide_averages()`
     * ```
     * @var string
     */
    public $other = '';

    /**
     * 视图模板对象
     * @var null
     */
    private $view = null;

    /**
     * 初始化变量
     */
    public function init()
    {
        parent::init(); // TODO: Change the autogenerated stub

        $this->view = $this->getView();
        $this->data = $this->data ? $this->data : $this->config['data'];

        if (isset($this->config['data'])) unset($this->config['data']);
        if (empty($this->data))
            throw new Exception('Chart\'s data can not be empty');
    }

    /**
     * 入口
     */
    public function run()
    {
        parent::run(); // TODO: Change the autogenerated stub

        $js = $this->getJs();
        $this->registerAsset()->registerJs($js);
    }

    /**
     * 获取js
     * @return string
     */
    protected function getJs()
    {
        $data = $this->array2json($this->getData());
        $config = $this->array2js($this->getConfig());
        $other = $this->getOther();
        $js = sprintf(";var data = %s;var chart = new Chart(%s);%s;", $data, $config, $other);
        return $js;
    }

    /**
     * 注册静态资源
     * @return $this
     */
    protected function registerAsset()
    {
        ChartsAsset::register($this->view);
        return $this;
    }

    /**
     * 注册js代码
     * @param $js
     * @return $this
     */
    protected function registerJs($js)
    {
        $this->view->registerJs($js);
        return $this;
    }

    /**
     * 获取图标数据
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * 获取配置参数
     * @return array
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * 获取配置参数other的数据
     * @return string
     */
    public function getOther()
    {
        return $this->other;
    }

    /**
     * php数组转json格式
     * @param array $data
     * @return string
     */
    protected function array2json(array $data)
    {
        return json_encode($data);
    }

    /**
     * 截取字符串
     * 如果字符串中存在"<%"和"%>"符号，则截取两符号中的内容
     *
     * 例子：
     * 输入：
     *      <%d => (d + '').toUpperCase()%>
     * 返回：
     *      d => (d + '').toUpperCase()
     * 输入：
     *      #chart
     * 返回：
     *      #chart
     *
     *
     * **用于生成原生js代码**
     *
     * @param $string string 包含"<%"和"%>"符号的字符串
     * @return string
     */
    protected function substr($string)
    {
        $pos_start = strpos($string, '<%') + 2;
        $pos_end = strpos($string, '%>');

        if ($pos_start === false || $pos_end === false)
            return "'$string'";

        return substr($string, $pos_start, $pos_end-$pos_start);
    }

    /**
     * php数组转原生js代码
     * @param array $data
     * @return string
     */
    protected function array2js(array $data)
    {
        $js = '{';
        foreach ($data as $k=>$v) {
            if (is_array($v)) {
                $tmp = '[';
                foreach ($v as $_v) {
                    if (is_int($_v))
                        $tmp .= $_v;
                    else
                        $tmp .= $this->substr($_v); // $_v string
                    $tmp .= ',';
                }
                $v = rtrim($tmp, ',') . ']';
            } else if (is_string($v)){
                $v = $this->substr($v); // $v string
            }

            $js .= "{$k}: $v, ";
        }
        $js .= 'data: data'; // 添加数据
        return $js . '}';
    }

}