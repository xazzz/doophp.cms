<? if(!defined('LUA_ROOT')) exit('Access Denied'); include Lua::display('_head',$this->dir); ?>
<div class="luatop">
	<div class="position">Lua：CMS  > <a href="./plugin.htm">插件管理</a> > 所有插件</div>
</div>
<div class="clear"></div>

<div class="stat_list" style="padding-left:24px;margin:0px;">
	<ul>
		<li class="now"><a href="./plugin.htm">已安装插件</a></li>
		<li><a href="./plugin.htm?action=market">插件市场</a></li>
	</ul>
</div>
<div style="clear:both;"></div>

<div class="luabox">
	<ul class="columnlist"><? if(is_array($ps)) { foreach($ps as $v) { ?>		<li class="contlist">
			<div class="box">
				<div class="img" style="text-align:center;"><a href="./plugin.htm?action=<?=$v['act']?>"><img src="<?=$v['ico']?>"  width="64" height="64"/></a></div>
				<h2 class="title"><a href="./plugin.htm?action=<?=$v['act']?>"><?=$v['name']?></a><br /><font color='gray'>(<?=$v['act']?>)</font></h2>
			</div>
		</li>
		<? } } ?></ul>
</div>
<? include Lua::display('_foot',$this->dir); ?>
