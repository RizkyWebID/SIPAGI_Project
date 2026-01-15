#!/bin/bash

# Masuk ke direktori project agar script tahu di mana dia bekerja
cd /opt/lampp/htdocs/SIPAGI_Project

while true
do
  # 1. Tarik data terbaru untuk menghindari konflik (Force Pull)
  git pull origin main --rebase

  # 2. Cek apakah ada perubahan file
  if [[ -n $(git status -s) ]]; then
    echo "Perubahan terdeteksi, mengirim ke GitHub..."
    git add .
    git commit -m "SIPAGI Auto-Update: $(date '+%Y-%m-%d %H:%M:%S')"
    git push origin main
    echo "Berhasil update pada $(date)"
  else
    echo "Tidak ada perubahan. Standby..."
  fi

  # 3. Jeda 5 menit (300 detik)
  sleep 300
done