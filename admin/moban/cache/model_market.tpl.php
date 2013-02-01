<? if(!defined('LUA_ROOT')) exit('Access Denied'); include Lua::display('_head',$this->dir); ?>
<div class="luatop">
	<div class="position">Lua：CMS  > <a href="./model.htm">模型管理</a></div>
</div>
<div class="clear"></div>

<div class="stat_list" style="padding-left:24px;margin:0px;">
	<ul>
		<li><a href="./model.htm">已安装模型</a></li><? if(is_array($this->mtype)) { foreach($this->mtype as $k => $v) { ?><li class="<? if($k==$mtype) { ?>now<? } ?>"><a href="./model.htm?mtype=<?=$k?>"><?php echo isset($v) ? $v : "";?>模型</a></li><? } } ?><li class="now"><a href="./model.htm?action=market">模型市场</a></li>
	</ul>
</div>
<div style="clear:both;"></div>

<table cellpadding="2" cellspacing="1" class="table">
	<tr>
		<td class="centle" colspan="8" style=" height:20px; line-height:30px; font-weight:normal; padding-left:10px;">
			<div style="float:left;">感谢大家的共享! </div>
			<div style="float:right;">更多模型请访问：<a href="http://www.luacms.com/" target='_blank'>Luacms</a></div>
		</td>
	</tr>
</table>
<? include Lua::display('_foot',$this->dir); ?>
