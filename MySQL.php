<?php

header("Content-Type:text/html;charset=utf-8");

/**
 * Created by PhpStorm.
 * User: 南宫天明
 * Date: 2016/8/12 0012
 * Time: 14:08
 */
//数据库操作类

/*
Report all errors except E_NOTICE
禁用notice警告
*/

error_reporting(E_ALL^E_NOTICE);

/**
 * Class MySQL
 * 数据库类
 * 实例化时传入一个 关联数组 包括
 * 主机
 * 端口号
 * 用户名
 * 密码
 * 要使用的数据库
 * 以及 是否调试
 */
class MySQL
{
    private $link;//数据库连接
    private $result;//返回结果
    private $host;//主机
    private $port;//端口
    private $user;//数据库用户名
    private $password;//数据库密码
    private $db;//要使用的数据库
    private $debug = false;//是否调试 (内部专用)
    private $sql_type;//判断不同sql 后面会用到
    private $prefix = "";//表名前缀

    /**
     * MySQL constructor. 数据库构造方法
     * @param array $config
     *              $config['host'] 主机
     *              $config['port'] 端口
     *              $config['user'] 数据库用户名
     *              $config['password'] 数据库密码
     *              $config['db'] 要使用的数据库
     *              $config['prefix'] 表名前缀
     *              $config['debug'] 是否调试
     */

    public function __construct($config = array("host"=>"","port"=>"","user"=>"","password"=>"","db"=>"","prefix"=>"","debug"=>""))
    {
        $this->host = $config['host'] ? $config['host'] : "127.0.0.1";
        $this->port = $config['port'] ? $config['port'] : 3306;
        $this->user = $config['user'] ? $config['user'] : "root";
        $this->password = $config['password'] ? $config['password'] : "root";
        $this->db = $config['db'] ? $config['db'] : "test";
        $this->prefix = $config['prefix'] ? $config['prefix'] : $this->prefix;
        $this->debug = $config['debug'] ? $config['debug'] : $this->debug;
        $this->link = new MySQLi();
        if($this->connect())
        {
            if($this->select_db())
            {
                return mysqli_set_charset($this->link , "utf8" );
            }
            else
            {
                echo "数据库不存在";
            };
        }
        else
        {
            die;
        };
    }

    /**
     * 析构函数 关闭mysql连接
     */
    public function __destruct()
    {
        // TODO: Implement __destruct() method.
        $this->close_link();
    }

    /**
     * 连接数据库
     * @return bool
     */
    private function connect()
    {
        @$this->link->connect($this->host,$this->user,$this->password,$this->db,$this->port);
        if(mysqli_connect_error())
        {
            $connect_error = mb_convert_encoding(mysqli_connect_error(),'UTF-8','GBK');
            $connect_error = '数据库连接错误 --->  错误编码:'.mysqli_connect_errno().' && 错误信息:'.$connect_error."\n";
            echo $connect_error;
            $this->error_log($connect_error);
            $this->link = false;
            return false;
        }
        else
        {
            return true;
        }
    }

    /**
     * 查询数据库
     * 成功返回true 失败返回 false
     * @return bool
     */
    private function select_db()
    {
        return $this->link->select_db($this->db);
    }

    /**
     * 关闭数据库
     * @return bool
     */

    private function close_link()
    {
        if($this->link)
        {
            return $this->link->close();
        }
    }

    /**
     * 返回结果集
     * @param string $set
     * @return array|object
     */
    private function result_set($set = "A")
    {
        $set = $set == "A" ? MYSQLI_ASSOC :( $set == "N" ? MYSQLI_NUM : MYSQLI_BOTH);

        $result = array();
        while($row = mysqli_fetch_array($this->result,$set)) {
            $result[] = $row;
        }
        //释放结果集
        mysqli_free_result($this->result);
        return $result;
    }

    /**
     * 返回影响行数或false
     * @return bool|int
     */
    private function result_num()
    {
        if ($this->result)
        {
            return $this->result;
        }
        else
        {
            return false;
        }
    }

    private function sql_log($sql)
    {
        $sql_log = "sql.log";
        $fp = fopen($sql_log,"a+");
        return fwrite($fp,$sql);
    }

    private function html_log($text)
    {
        $html_log = "log.html";
        $fp = fopen($html_log,"a+");
        return fwrite($fp,$text);
    }

    private function error_log($err)
    {
        $error_log = "err.log";
        $fp = fopen($error_log,"a+");
        return fwrite($fp,date('Y/m/d H:i:s').":\n$err");
    }

    //sql语句预处理
    public function query($sql)
    {
        $now = date('Y/m/d H:i:s');
        $this->sql_log("$now\t'$sql'\n");
        if($this->debug)
        {
            $time_start = microtime();//开始计时, 放在程序头
            $result = $this->link->query($sql);
            $time_end = microtime();//结束计时, 放在尾部
            $time = $time_end - $time_start;
            $style = "<style>.log{margin:0 auto;position:fixed;bottom:0;right: 0} .title{background-color: #2a85a0} table{color: #81e2a7}</style>";
            $text = "<table class='log' border='1' cellspacing='0' bgcolor='#6bbbbd'>
        <tr>
            <td class='title'>时间</td>
            <td>$now</td>
        </tr>
        <tr>
            <td class='title'>SQL语句</td>
            <td>$sql</td>
        </tr>
        <tr>
            <td class='title'>执行时间</td>
            <td>$time us</td>
        </tr>
        <tr>
            <td class='title'>返回结果</td>
            <td>";

            if(is_array($result) || is_object($result))
            {
                $text .= 'array';
            }
            else if(is_object($result))
            {
                $text .= 'object';
            }
            else
            {
                $text .= $result;
            }

            $text .= "</td>
        </tr>
        <tr>
            <td class='title'>错误代码</td>
            <td>".$this->link->errno."</td>
        </tr>
        <tr>
            <td class='title'>错误信息</td>
            <td>".mysqli_error($this->link)."</td>
        </tr>";
            $text .= "\n</table>\n";
            $this->error_log("错误代码:".$this->link->errno."\n错误信息".mysqli_error($this->link)."\n");
            $this->html_log($text);
            echo $style.$text;
        }
        else
        {
            $this->error_log("错误代码:".$this->link->errno."\n错误信息".mysqli_error($this->link)."\n");
            $result = $this->link->query($sql);
        }
        return $result;
    }

    /**
     * 执行查询语句
     * @param string $sql 要执行的select语句
     * @return array|object 返回对象或数组
     */
    public function select($sql)
    {
        $result = $this->query($sql);
        if(!$result)
        {
            error_log(mysqli_error($this->link));
            return false;
        }
        $this->result = $result ;
        $res = $this->result_set("A");
        return $res;
    }

    /**
     * 执行修改删除语句
     * @param string $sql 要执行的增删改语句
     * @return bool|int 成功返回影响行数 失败返回false
     */
    public function sql_query($sql)
    {
        $result = $this->query($sql);
        if(!$result)
        {
            error_log(mysqli_error($this->link));
            return false;
        }
        $this->result = $this->link->affected_rows;
        $res = $this->result_num();
        return $res;
    }

    /**
     * 查询某表的符合条件的数据
     * @param string $table 表名称
     * @param array|string $field 字段名称 传索引数组或字符串
     * @param array|string $where 查询条件 传关联数组或字符串
     * @param array|string $order 排序字段 传索引数组或字符串
     * @param array|string $limit 分页查询 传索引数组或字符串
     * @return array|object
     */
    public function select_array($table,$field="",$where="1",$order="",$limit="")
    {

        $sql = "SELECT ";
        if(is_array($field))
        {
            $fields = "";
            for($i = 0;$i<count($field);$i++)
            {
                $fields .="$field[$i],";
            }
            $fields = substr($fields,0,strlen($fields)-1);
            $sql .="$fields FROM $this->prefix.$table ";
        }
        else
        {
            if(empty($field)) {
                $field = "*";
            }

            $sql .="$field FROM $this->prefix.$table ";
        }

        if(is_array($where))
        {
            $wheres = "";
            foreach($where as $key=>$value)
            {
                $wheres .= "$key=$value AND ";
            }
            $wheres = substr($wheres,0,strlen($wheres)-4);
            $sql .= "WHERE $wheres ";
        }
        else
        {
            if(empty($where)) {
                $where = "1";
            }

            $sql .= "WHERE $where ";
        }

        if(is_array($order))
        {
            $orders = "";
            for($i = 0;$i<count($order);$i++)
            {
                $orders .="$order[$i],";
            }
            $orders = substr($orders,0,strlen($orders)-1);
            $sql .= "ORDER BY $orders ";
        }
        else
        {
            if(!empty($order))
            {
                $sql .= "ORDER BY $order ";
            }
        }

        if(is_array($limit))
        {
            $limits = "";
            for($i = 0;$i<count($limit);$i++)
            {
                $limits .="$limit[$i],";
            }
            $limits = substr($limits,0,strlen($limits)-1);
            $sql .= "LIMIT $limits";
        }
        else
        {
            if(!empty($limit))
            {
                $sql .= "LIMIT $limit";
            }
        }

        return $this->select($sql);
    }

    /**
     * 执行增加语句
     *      增加一条
     *      传关联数组或字符串
     *      字段传字符串或不传
     * ======================
     *      如要增加多条就
     *      传二维索引数组或字符串
     *      字段传索引数组或不传
     * @param string $table 要增加的表名
     * @param string|array $value 要增加的值
     * @param string|array $field 字段
     * @return bool|int 成功返回影响行数 失败返回false
     */
    public function insert($table,$value,$field="")
    {
        $sql = "INSERT INTO $this->prefix.$table (";
        if(is_array($value))//如果传的数组
        {
            $keys =  array_keys($value);
            $type = array_sum($keys) == array_sum(array_keys($keys))? 'NUM' : 'ASSOC';
            if($type == "NUM")//如果传的索引数组
            {
                if(is_array($field))
                {
                    for($i = 0; $i < count($field);$i ++)
                    {
                        $sql .="$field[$i],";
                    }
                    $sql = substr($sql,0,strlen($sql)-1);
                    $sql .= ") VALUES (";
                    for($i = 0; $i < count($value);$i ++)
                    {
                        if(is_array($value[$i]))
                        {
                            $this->sql_type = true;
                            $values = $value[$i];
                            for($j = 0; $j < count($values);$j ++)
                            {
                                $sql .= "$values[$j],";

                            }
                            $sql = substr($sql,0,strlen($sql)-1);
                            $sql .="),(";
                        }
                        else
                        {
                            $this->sql_type = false;
                            $sql .="$value[$i],";
                        }

                    }
                    if($this->sql_type)
                    {
                        $sql = substr($sql,0,strlen($sql)-2);
                    }
                    else
                    {
                        $sql = substr($sql,0,strlen($sql)-1);
                        $sql .= ")";
                    }
                }
                else
                {
                    $sql2 = $sql;
                    $sql .=") VALUES (";
                    for($i = 0; $i < count($value);$i ++)
                    {

                        if(is_array($value[$i]))
                        {
                            $values = $value[$i];
                                $this->sql_type = true;
                                for($j = 0; $j < count($values);$j ++)
                                {
                                    $sql .= "$values[$j],";

                                }
                                $sql = substr($sql,0,strlen($sql)-1);
                                $sql .="),(";
                        }
                        else
                        {
                            $this->sql_type = false;
                            $sql .="$value[$i],";
                        }
                    }
                    if($this->sql_type)
                    {
                        $sql = substr($sql,0,strlen($sql)-2);
                    }
                    else
                    {
                        $sql = substr($sql,0,strlen($sql)-1);
                        $sql .= ")";
                    }
                }
            }
            else
            {
                $keys = array_keys($value);
                for($i = 0;$i<count($keys);$i++)
                {
                    $sql .="$keys[$i],";
                }
                $sql = substr($sql,0,strlen($sql)-1);
                $sql .= ") VALUES (";
                for($i = 0;$i<count($keys);$i++)
                {
                    $k = $keys[$i];
                    $sql .="$value[$k],";
                }
                $sql = substr($sql,0,strlen($sql)-1);
                $sql .= ")";

            }
        }
        else
        {
            $sql .= "$field) VALUES $value";
        }
        return $this->sql_query($sql);
    }

    /**
     * 执行删除语句
     * @param string $table 要执行删除的表名
     * @param string|array $where 删除的条件 可以传关联数组或字符串
     * @param string $either 关系副词 默认 AND
     * @return bool|int 成功返回影响行数 失败返回false
     */
    public function delete($table,$where="",$either="AND")
    {
        $sql = "DELETE FROM $this->prefix.$table ";
        if(!empty($where))
        {
            $sql .= "WHERE ";
            if(is_array($where))
            {
                if($either=="AND")
                {
                    $this->sql_type = 1;
                }
                else
                {
                    $this->sql_type = 2;
                }
                foreach($where as $key=>$value)
                {
                    $sql .= "$key=$value $either ";
                }
            }
            else
            {
                $this->sql_type = 3;
                $sql .= $where;
            }

            switch($this->sql_type)
            {
                case 1:
                    $sql = substr($sql,0,strlen($sql)-4);
                    break;
                case 2:
                    $sql = substr($sql,0,strlen($sql)-3);
                    break;
                case 3:
                    break;
                default:
                    break;

            }
        }
        return $this->sql_query($sql);
    }

    /**
     * 执行修改语句
     * @param string $table 要修改的表名
     * @param string|array $set 要修改的值 可以传关联数组或字符串
     * @param string|array $where 修改的条件 可以传关联数组或字符串
     * @param string $either 关系副词 默认 AND
     * @return bool|int 成功返回影响行数 失败返回false
     */
    function update($table,$set,$where="",$either="AND")
    {
        $sql = "UPDATE $table SET ";
        if(is_array($set))
        {
            foreach($set as $key=>$value)
            {
                $sql .= "$key=$value,";
            }
            $sql = substr($sql,0,strlen($sql)-1);
            if(!empty($where))
            {
                $sql .=" WHERE ";
                if(is_array($where))
                {
                    if($either=="AND")
                    {
                        $this->sql_type = 1;
                    }
                    else
                    {
                        $this->sql_type = 2;
                    }
                    foreach($where as $k=>$v)
                    {
                        $sql .= "$k=$v $either ";
                    }
                }
                else
                {
                    $this->sql_type = 3;
                    $sql .=" $where";
                }
            }
            switch($this->sql_type)
            {
                case 1:
                    $sql = substr($sql,0,strlen($sql)-4);
                    break;
                case 2:
                    $sql = substr($sql,0,strlen($sql)-3);
                    break;
                case 3:
                    break;
                default:
                    break;
            }
        }
        else
        {
            $sql .= $set;
            if(!empty($where))
            {
                $sql .=" WHERE ";
                if(is_array($where))
                {
                    if($either=="AND")
                    {
                        $this->sql_type = 1;
                    }
                    else
                    {
                        $this->sql_type = 2;
                    }
                    foreach($where as $k=>$v)
                    {
                        $sql .= "$k=$v $either ";
                    }
                }
                else
                {
                    $this->sql_type = 3;
                    $sql .="$where";
                }
                switch($this->sql_type)
                {
                    case 1:
                        $sql = substr($sql,0,strlen($sql)-4);
                        break;
                    case 2:
                        $sql = substr($sql,0,strlen($sql)-3);
                        break;
                    case 3:
                        break;
                    default:
                        break;
                }
            }
        }
        return $this->sql_query($sql);
    }

}

/*
 * 输出或打印制定数组或字符
 * @name : output
 * @description : output debug
 * @param $var : input data
 * @return void
 */
Function _S($var = null, $dump = false) {
    $func = $dump ? 'var_dump' : 'print_r';
    if(empty($var))
    {
        echo 'null';
        echo '<br/>';
    }
    elseif(is_array($var) || is_object($var))
    {
        //array,object
        echo '<pre>';
        $func($var);
        echo '</pre>';
        echo '<br/>';
    }
    else
    {
        //string,int,float...
        $func($var);
        echo '<br/>';
    }
}
