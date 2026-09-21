# Hostinger DNS — create A record for live.tadrislab.com
# Requires API token from https://hpanel.hostinger.com → Account → API
#
# Usage (PowerShell):
#   $env:HOSTINGER_API_TOKEN = "your-token"
#   .\scripts\hostinger-create-live-tadrislab-dns.ps1

param(
    [string]$Domain = "tadrislab.com",
    [string]$Subdomain = "live",
    [string]$Ip = "187.124.36.228",
    [string]$Token = $env:HOSTINGER_API_TOKEN
)

if (-not $Token) {
    Write-Host "HOSTINGER_API_TOKEN is missing."
    Write-Host "Manual step in hPanel DNS for $Domain :"
    Write-Host "  Type=A  Name=$Subdomain  Value=$Ip  TTL=300"
    Write-Host "Result: $Subdomain.$Domain -> $Ip"
    exit 2
}

$fqdn = "$Subdomain.$Domain"
$headers = @{
    Authorization = "Bearer $Token"
    "Content-Type" = "application/json"
}

# Hostinger DNS API v1 (zones)
$body = @{
    overwrite = $true
    zone = @(
        @{
            name = $Subdomain
            type = "A"
            ttl = 300
            records = @(@{ content = $Ip })
        }
    )
} | ConvertTo-Json -Depth 6

try {
    $uri = "https://developers.hostinger.com/api/dns/v1/zones/$Domain"
    # Prefer overwrite endpoint used by Hostinger DNS API
    $response = Invoke-RestMethod -Method Put -Uri $uri -Headers $headers -Body $body
    Write-Host "DNS upsert submitted for $fqdn -> $Ip"
    $response | ConvertTo-Json -Depth 6
} catch {
    Write-Host "API call failed: $($_.Exception.Message)"
    Write-Host "Fall back to hPanel: A / $Subdomain / $Ip"
    exit 1
}

Start-Sleep -Seconds 3
nslookup $fqdn
