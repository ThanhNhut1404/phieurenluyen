@echo off
netsh advfirewall firewall add rule name="Open Port 80 for XAMPP" dir=in action=allow protocol=TCP localport=80
echo "Da mo tuong lua cho ket noi dien thoai thanh cong!"
pause
