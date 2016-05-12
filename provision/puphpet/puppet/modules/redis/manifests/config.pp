# = Class: redis::config
#
# This class provides configuration for Redis.
#
class redis::config {
  $activerehashing              = $::redis::activerehashing
  $appendfsync                  = $::redis::appendfsync
  $appendonly                   = $::redis::appendonly
  $auto_aof_rewrite_min_size    = $::redis::auto_aof_rewrite_min_size
  $auto_aof_rewrite_percentage  = $::redis::auto_aof_rewrite_percentage
  $bind                         = $::redis::bind
  $cluster_config_file          = $::redis::cluster_config_file
  $cluster_enabled              = $::redis::cluster_enabled
  $cluster_node_timeout         = $::redis::cluster_node_timeout
  $daemonize                    = $::redis::daemonize
  $databases                    = $::redis::databases
  $dbfilename                   = $::redis::dbfilename
  $extra_config_file            = $::redis::extra_config_file
  $hash_max_ziplist_entries     = $::redis::hash_max_ziplist_entries
  $hash_max_ziplist_value       = $::redis::hash_max_ziplist_value
  $hz                           = $::redis::hz
  $list_max_ziplist_entries     = $::redis::list_max_ziplist_entries
  $list_max_ziplist_value       = $::redis::list_max_ziplist_value
  $log_file                     = $::redis::log_file
  $log_level                    = $::redis::log_level
  $masterauth                   = $::redis::masterauth
  $maxclients                   = $::redis::maxclients
  $maxmemory                    = $::redis::maxmemory
  $maxmemory_policy             = $::redis::maxmemory_policy
  $maxmemory_samples            = $::redis::maxmemory_samples
  $no_appendfsync_on_rewrite    = $::redis::no_appendfsync_on_rewrite
  $pid_file                     = $::redis::pid_file
  $port                         = $::redis::port
  $rdbcompression               = $::redis::rdbcompression
  $repl_timeout                 = $::redis::repl_timeout
  $requirepass                  = $::redis::requirepass
  $save_db_to_disk              = $::redis::save_db_to_disk
  $set_max_intset_entries       = $::redis::set_max_intset_entries
  $slave_read_only              = $::redis::slave_read_only
  $slave_serve_stale_data       = $::redis::slave_serve_stale_data
  $slaveof                      = $::redis::slaveof
  $slowlog_log_slower_than      = $::redis::slowlog_log_slower_than
  $slowlog_max_len              = $::redis::slowlog_max_len
  $stop_writes_on_bgsave_error  = $::redis::stop_writes_on_bgsave_error
  $syslog_enabled               = $::redis::syslog_enabled
  $syslog_facility              = $::redis::syslog_facility
  $tcp_keepalive                = $::redis::tcp_keepalive
  $timeout                      = $::redis::timeout
  $workdir                      = $::redis::workdir
  $zset_max_ziplist_entries     = $::redis::zset_max_ziplist_entries
  $zset_max_ziplist_value       = $::redis::zset_max_ziplist_value

  if $::redis::notify_service {
    File {
      owner  => $::redis::config_owner,
      group  => $::redis::config_group,
      mode   => $::redis::config_file_mode,
      notify => Service[$::redis::service_name]
    }
  } else {
    File {
      owner => $::redis::config_owner,
      group => $::redis::config_group,
      mode  => $::redis::config_file_mode,
    }
  }

  file {
    $::redis::config_dir:
      ensure => directory,
      mode   => $::redis::config_dir_mode;

    $::redis::config_file:
      ensure  => present,
      content => template($::redis::conf_template);

    $::redis::log_dir:
      ensure => directory,
      group  => $::redis::service_group,
      mode   => $::redis::log_dir_mode,
      owner  => $::redis::service_user;
  }

  # Adjust /etc/default/redis-server on Debian systems
  case $::osfamily {
    'Debian': {
      file { '/etc/default/redis-server':
        ensure => present,
        group  => $::redis::config_group,
        mode   => $::redis::config_file_mode,
        owner  => $::redis::config_owner,
      }

      if $::redis::ulimit {
        augeas { 'redis ulimit' :
          context => '/files/etc/default/redis-server',
          changes => "set ULIMIT ${::redis::ulimit}",
        }
      }
    }

    default: {
    }
  }
}

