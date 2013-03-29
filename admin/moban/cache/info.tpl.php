<? if(!defined('LUA_ROOT')) exit('Access Denied'); include Lua::display('_head',$this->dir); ?>
<div class="luatop">
	<div class="position">Doo：CMS  > <a href="./info.htm">系统信息</a></div>
</div>
<div class="clear"></div>
<div class="sysadmin">
	<div class="topbox">
	<div class="boxlist listxp">
		<div style="margin-left:8px;">
		<h2>概况</h2>
		<div class="text">
			<h3>用户信息</h3>
			<ul class="user">
				<li><span>用户名：</span><? echo $this->user['username']; ?></li>
				<li><span>登录次数：</span><? echo $this->user['logs']; ?></li>
				<li><span>IP：</span><? echo $this->user['loginip']; ?></li>
				<li style="clear:both;"><br/><span>登录时间：</span><? echo date('Y-m-d H:i:s',$this->user['logintime']); ?></li>
			</ul>
			<div class="clear"></div>
			<h3 style="margin-top:2px;">服务与支持</h3>
			<div class="fuwu">
				<a href="http://www.doophp.net/forum/" target="_blank">技术交流</a><span>-</span><a href="http://www.doophp.net/jack/doc" target="_blank">用户指南</a><span>-</span><a href="http://www.doophp.net" target="_blank">DooPHP中国</a><span>-</span><a href="http://www.doophp.net/jack/download/" target="_blank">Doo下载</a><span>-</span><a href="https://github.com/cyobason/doophp.cms" target="_blank">DooPHP.Cms开发框架下载</a>
			</div>
		</div>
		</div>
	</div>
	<div class="boxlist listxr">
		<div style="margin-right:8px;">
		<h2>团队介绍</h2>
		<div class="text dengao">
			<div class="mtsv">
				<h3>总策划：</h3>
				<p><a href="http://weibo.com/migao100/" target="_blank">米高100</a></p>
			</div>
			<div class="mtsv">
				<h3>开发团队：</h3>
				<p><a href="http://weibo.com/phpcodes" target="_blank">神飞的梦</a> <a href="http://weibo.com/chenxyhz" target="_blank">斑点牛牛</a></p>
			</div>
			<div class="mtsv">
				<h3>美工设计：</h3>
				<p><a href="http://weibo.com/sunve93" target="_blank">sunve93</a></p>
			</div>
			<div class="mtsv">
				<h3>特别鸣谢：</h3>
				<p><a href="http://t.qq.com/phpwhy" target="_blank">易得</a> <a href="http://weibo.com/521365345" target="_blank">老蜗牛</a></p>
			</div>
			<div class="mtsv">
				<h3>旗下网站：</h3>
				<p><a href="http://www.doophp.net/" target="_blank">DooPHP中国</a> <a href="http://dbx.cc/" target="_blank">东邦WEB开发</a></p>
			</div>
			<div class="clear"></div>
		</div>
		</div>
	</div>
	<div class="clear"></div>
	</div>
	<div class="boxlist listxp">
		<div style="margin-left:8px;">
		<h2>服务器信息</h2>
		<div class="text dengao">
			<div class="mtsv">
				<h3>程序名称：</h3>
				<p><span>DooPHP.Cms</span>网站内容管理系统 - <font color="red">Doo</font>(简称)</p>
			</div>
			<div class="mtsv">
				<h3>系统版本：</h3>
				<p><?=LUA_VER?></p>
			</div>
			<div class="mtsv">
				<h3>操作系统：</h3>
				<p><?=PHP_OS?></p>
			</div>
			<div class="mtsv">
				<h3>PHP环境：</h3>
				<p><?php echo isset($_SERVER['SERVER_SOFTWARE']) ? $_SERVER['SERVER_SOFTWARE'] : "";?></p>
			</div>
			<div class="mtsv">
				<h3>Mysql版本：</h3>
				<p><?php echo isset($my) ? $my : "";?></p>
			</div>
			<div class="mtsv">
			<h3>版权所有：</h3>
	        <p>DooPHP中国</p>
			</div>
			<div class="clear"></div>
		</div>
		</div>
	</div>
	<div class="boxlist listxr">
		<div style="margin-right:8px;">
		<h2>DooPHP</h2>
		<div class="text dengao">
			<ol class="xieyi">
			<li><b>简介</b><br/>DooPHP 是一个敏捷的轻量级开源 PHP 开发框架，全部文件加起来不超过1M。</li>
			<li><b>特点</b><br/>MVC框架、RESTful API、REST 客户端、URL路由、ORM映射工具 ...</li>
			<li><b>运行环境</b><br/>要求运行环境支持 PHP5.1 或者更高版本。</li>
			</ol>
		</div>
		</div>
	</div>
	<div class="clear"></div>
</div>
<? include Lua::display('_foot',$this->dir); ?>
