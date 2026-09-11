@REM Copilotで利用するためOneDriveへコピーする
@REM 日本語文字を含むファイル名を利用するためUTF-8(65001)に変更
chcp 65001
ROBOCOPY "E:\www\imawa\net\hpl\baseball\documents" "E:\Users\Akinobu\OneDrive\Documents\SideBusiness\SoundLine\HPL\野球大会管理" /E
ROBOCOPY "E:\www\imawa\net\hpl\baseball" "E:\Users\Akinobu\OneDrive\Documents\SideBusiness\SoundLine\HPL\野球大会管理\Source" *.PHP /E
