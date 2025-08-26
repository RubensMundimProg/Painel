while(1 -eq 1){
	Start-Job -ScriptBlock {php C:\Zend\Apache2\htdocs\INEP-SVN\PAINEL\public\index.php twitter}
	Start-Sleep -s 200
}