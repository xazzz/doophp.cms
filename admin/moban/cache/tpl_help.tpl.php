<? if(!defined('LUA_ROOT')) exit('Access Denied'); include Lua::display('_head',$this->dir); ?>
<div class="luatop">
	<div class="position">Doo：CMS  > <a href="./tpl.htm">模板管理</a></div>
</div>
<div class="clear"></div>

<div id="showmessage"></div>

<div style="padding:20px;">
	引用模板：<?=$tag?><br />
</div>
<? include Lua::display('_foot',$this->dir); ?>
