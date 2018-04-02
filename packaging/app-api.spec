
Name: app-api
Epoch: 1
Version: 1.0.1
Release: 1%{dist}
Summary: API Server
License: GPLv3
Group: Applications/Apps
Source: %{name}-%{version}.tar.gz
Buildarch: noarch
Requires: %{name}-core = 1:%{version}-%{release}
Requires: app-base

%description
API Server for ClearOS.

%package core
Summary: API Server - API
License: LGPLv3
Group: Applications/API
Requires: app-base-core
Requires: clearos-framework >= 7.4.8

%description core
API Server for ClearOS.

This package provides the core API and libraries.

%prep
%setup -q
%build

%install
mkdir -p -m 755 %{buildroot}/usr/clearos/apps/api
cp -r * %{buildroot}/usr/clearos/apps/api/

install -D -m 0755 packaging/api %{buildroot}/usr/bin/api
install -D -m 0644 packaging/api.acl %{buildroot}/var/clearos/base/access_control/public/api

%post
logger -p local6.notice -t installer 'app-api - installing'

%post core
logger -p local6.notice -t installer 'app-api-core - installing'

if [ $1 -eq 1 ]; then
    [ -x /usr/clearos/apps/api/deploy/install ] && /usr/clearos/apps/api/deploy/install
fi

[ -x /usr/clearos/apps/api/deploy/upgrade ] && /usr/clearos/apps/api/deploy/upgrade

exit 0

%preun
if [ $1 -eq 0 ]; then
    logger -p local6.notice -t installer 'app-api - uninstalling'
fi

%preun core
if [ $1 -eq 0 ]; then
    logger -p local6.notice -t installer 'app-api-core - uninstalling'
    [ -x /usr/clearos/apps/api/deploy/uninstall ] && /usr/clearos/apps/api/deploy/uninstall
fi

exit 0

%files
%defattr(-,root,root)
/usr/clearos/apps/api/controllers
/usr/clearos/apps/api/htdocs
/usr/clearos/apps/api/views

%files core
%defattr(-,root,root)
%exclude /usr/clearos/apps/api/packaging
%exclude /usr/clearos/apps/api/unify.json
%dir /usr/clearos/apps/api
/usr/clearos/apps/api/deploy
/usr/clearos/apps/api/language
/usr/clearos/apps/api/libraries
/usr/bin/api
/var/clearos/base/access_control/public/api
