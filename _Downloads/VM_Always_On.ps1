# VIRTUALBOX INSTALLATION PATH; CHANGE IF NOT INSTALLED IN THE DEFAULT LOCATION
$vboxPath = ($Env:Programfiles +'\Oracle\VirtualBox\')
# CASE SENSITIVE NAME OR UUID OF VMS TO KEEP RUNNING
# ex @('Vm1','VM2','vm3','cfeb596c-3e21-1eec-a015-a496559a3d11')
$alwaysRunningVMs = @('')

# GET A LIST OF CURRENTLY RUNNING VMS
$runningVMs = Invoke-Expression ('& "'+ $vboxPath +'vboxmanage.exe" list runningvms')
Write-Host 'VMs Currently Running' -ForegroundColor 'Green'
Write-Host '-----------------------------------------------' -ForegroundColor 'Green'
Write-Host $runningVMs -ForegroundColor 'Green'
Write-Host '-----------------------------------------------' -ForegroundColor 'Green'
Write-Host

# LOOP THROUGH $alwaysRunningVMs AND COMPARE TO RUNNING VMS
for($i=0; $i -lt $alwaysRunningVMs.length; $i++){
	Write-Host ('*** Checking "'+ $alwaysRunningVMs[$i] +'"')
	if($runningVMs -like ('*"'+ $alwaysRunningVMs[$i] +'"*') -or $runningVMs -like ('*{'+ $alwaysRunningVMs[$i] +'}*')){
		Write-Host ("`t" + $alwaysRunningVMs[$i] +' is running')
	} else {
		Write-Host ("`tStarting "+ $alwaysRunningVMs[$i])
		Write-Host -NoNewline "`t"
		Invoke-Expression ('& "'+ $vboxPath +'vboxmanage.exe" startvm '+ $alwaysRunningVMs[$i] +' --type headless') 
	}
}