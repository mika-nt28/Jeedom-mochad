#!/bin/bash

INSTALL_DIR=/usr/local/bin
TEMP_DIR=`mktemp -d /tmp/mochad.XXXXXX`
MOCHAD_bin=$INSTALL_DIR/mochad
mochad_url="http://downloads.sourceforge.net/project/mochad/mochad-0.1.16.tar.gz?r=http%3A%2F%2Fsourceforge.net%2Fprojects%2Fmochad%2F&ts=1415019385&use_mirror=cznic"


check_run()  {
    "$@"
    local status=$?
    if [ $status -ne 0 ]; then
        echo "error with $1" >&2
	exit
    fi
    return $status
}

# Check for root priviledges
if [ $(id -u) != 0 ]
then
	echo "Superuser (root) priviledges are required to install mochad"
	echo "Please do 'sudo -s' first"
	exit 1
fi

echo "Installing additional libraries"
sudo apt-get -y --force-yes update
sudo apt-get -y --force-yes upgrade
sudo apt-get -y --force-yes dist-upgrade
sudo apt-get -y --force-yes install libusb-1.0.0-dev

#if [ "$(cat /etc/mochad/mochad_VERSION)" != "v0.1.16" ]
#then
echo "Getting mochad..."
cd $TEMP_DIR
check_run wget -q $mochad_url -O - | tar -zx

cd mochad-0.1.16

echo "Compiliing mochad..." 
check_run ./configure 

check_run make
check_run sudo make install

mkdir -p /etc/mochad
echo "v0.1.16" > /etc/mochad/mochad_VERSION

#fi
