# SIGA System Diagnostic Tools

## Overview

This comprehensive diagnostic toolkit has been created to identify the exact causes of SIGA system errors. All tools are **SAFE FOR PRODUCTION** - they only perform read-only tests and logging without modifying any system data or configurations.

## Quick Start

### Run All Diagnostics (Recommended)
```bash
php run_diagnostics.php
```
This master script runs all diagnostic tools in sequence and provides a comprehensive system health report.

### Run Individual Diagnostics
If you want to focus on specific areas:

```bash
# General system health
php system_diagnostic.php

# Database connectivity issues
php database_test.php

# PHP syntax problems
php syntax_checker.php

# Session management issues
php session_test.php

# Detailed error analysis
php error_debug.php
```

## Diagnostic Tools Description

### 1. `run_diagnostics.php` - Master Diagnostic Runner
- **Purpose**: Runs all diagnostic scripts in sequence
- **Output**: Comprehensive system health report
- **Use When**: First-time diagnosis or complete system check
- **Runtime**: 30-60 seconds

### 2. `system_diagnostic.php` - System Health Check
- **Purpose**: Tests critical system components
- **Checks**: File existence, PHP configuration, basic connectivity
- **Use When**: General system health assessment
- **Runtime**: 10-20 seconds

### 3. `database_test.php` - Database Connectivity Test
- **Purpose**: Specifically tests ODBC and database connectivity
- **Checks**: ODBC extension, DSN configuration, connection tests
- **Use When**: Database connection errors, ODBC issues
- **Runtime**: 5-15 seconds

### 4. `syntax_checker.php` - PHP Syntax Validation
- **Purpose**: Validates PHP syntax of all critical files
- **Checks**: Syntax errors, encoding issues, brace matching
- **Use When**: 500 errors, parse errors, syntax-related issues
- **Runtime**: 10-30 seconds

### 5. `session_test.php` - Session Functionality Test
- **Purpose**: Tests session management and authentication flow
- **Checks**: Session configuration, security functions, timeout settings
- **Use When**: Login issues, session problems, authentication errors
- **Runtime**: 5-10 seconds

### 6. `error_debug.php` - Enhanced Error Debugging
- **Purpose**: Provides detailed error reporting and analysis
- **Checks**: Error logs, memory usage, detailed error capture
- **Use When**: Intermittent errors, performance issues, detailed analysis needed
- **Runtime**: 15-30 seconds

## Understanding the Output

### Status Indicators
- **✓** - Test passed successfully
- **✗** - Test failed (critical issue)
- **!** - Warning (non-critical issue)

### Log Levels
- **INFO** - General information
- **WARNING** - Non-critical issues that should be reviewed
- **ERROR** - Critical issues requiring immediate attention
- **CRITICAL** - System-breaking issues

### Common Error Categories

#### Database Issues
- ODBC extension not loaded
- DSN configuration problems
- Database server connectivity
- Permission issues

#### PHP Syntax Errors
- Missing semicolons
- Unmatched braces or parentheses
- Incorrect PHP tags
- Encoding issues (UTF-8 BOM)

#### Session Problems
- Session save path permissions
- Session configuration conflicts
- Security function failures
- Cookie configuration issues

#### File System Issues
- Missing required files
- Incorrect file permissions
- Unreadable configuration files

## Troubleshooting Guide

### If You See CRITICAL ISSUES
1. **Run individual diagnostic scripts** to isolate the problem
2. **Fix syntax errors first** (use `syntax_checker.php` output)
3. **Resolve database connectivity** (use `database_test.php` output)
4. **Check file permissions** and ensure all required files exist
5. **Re-run diagnostics** after each fix to verify resolution

### If You See WARNINGS
1. **Review the specific warnings** in the detailed logs
2. **Address security-related warnings** first
3. **Fix performance-related issues** when possible
4. **Document any warnings** that cannot be immediately resolved

### If All Tests Pass
- Your system appears healthy
- The 500 errors may be caused by:
  - Specific user input combinations
  - Race conditions
  - External dependencies
  - Server configuration outside PHP

## Log Files

All diagnostic results are saved in the `/logs/` directory with timestamps:

- `master_diagnostic_YYYY-MM-DD_HH-MM-SS.log` - Complete diagnostic session
- `system_diagnostic_YYYY-MM-DD_HH-MM-SS.log` - System health details
- `database_test_YYYY-MM-DD_HH-MM-SS.log` - Database connectivity details
- `syntax_check_YYYY-MM-DD_HH-MM-SS.log` - Syntax validation details
- `session_test_YYYY-MM-DD_HH-MM-SS.log` - Session functionality details
- `error_debug_YYYY-MM-DD_HH-MM-SS.log` - Enhanced debugging details

## Important Notes

### Safety
- **All tools are read-only** - no system modifications are made
- **Safe for production environments** - no sensitive data is logged
- **No passwords or credentials** are displayed or logged
- **Temporary test data** is cleaned up automatically

### Performance
- **Minimal system impact** - diagnostics run quickly and efficiently
- **Memory usage is monitored** and reported
- **Scripts can be interrupted** safely if needed

### Privacy
- **No sensitive information** is captured in logs
- **Database credentials** are not displayed (only connection status)
- **User data** is not accessed or logged
- **IP addresses** are logged only for security event tracking

## Next Steps After Running Diagnostics

1. **Review the master diagnostic log** first for an overview
2. **Focus on CRITICAL and ERROR issues** before warnings
3. **Fix issues in this priority order**:
   - PHP syntax errors
   - Database connectivity
   - Missing files or permissions
   - Session configuration
   - Security settings
4. **Re-run diagnostics** after making changes
5. **Contact system administrator** if issues persist after fixes

## Support

If you need assistance interpreting the diagnostic results:

1. **Share the relevant log files** (they contain no sensitive data)
2. **Specify which errors you're experiencing** in the live system
3. **Include the timestamp** when errors occur
4. **Mention any recent changes** to the system or environment

The diagnostic logs provide comprehensive technical details that will help support teams quickly identify and resolve issues.