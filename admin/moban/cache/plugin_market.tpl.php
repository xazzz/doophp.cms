<? if(!defined('LUA_ROOT')) exit('Access Denied'); include Lua::display('_head',$this->dir); ?>
<div class="luatop">
	<div class="position">Doo：CMS  > <a href="./plugin.htm">插件管理</a> > 插件市场</div>
</div>
<div class="clear"></div>

<div class="stat_list" style="padding-left:24px;margin:0px;">
	<ul>
		<li><a href="./plugin.htm">已安装插件</a></li>
		<li class="now"><a href="./plugin.htm?action=market">插件市场</a></li>
	</ul>
</div>
<div style="clear:both;"></div>

<table cellpadding="2" cellspacing="1" class="table">
	<tr>
		<td class="centle" colspan="8" style=" height:20px; line-height:30px; font-weight:normal; padding-left:10px;">
			<div style="float:left;">感谢大家的共享! </div>
			<div style="float:right;">更多插件请访问：<a href="http://www.doophp.net/" target='_blank'>DooPHP</a></div>
		</td>
	</tr>
</table>
<? include Lua::display('_foot',$this->dir); ?>
