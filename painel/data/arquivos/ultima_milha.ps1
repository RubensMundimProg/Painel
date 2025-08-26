while(1 -eq 1){
	Start-Job -ScriptBlock {php C:\inetpub\wwwroot\painel\public\index.php dashboard}
	Start-Sleep -s 60
}