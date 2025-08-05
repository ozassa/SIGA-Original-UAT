# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

SIGA (Sistema Integrado de Gestão de Apólice) is a PHP-based web application for managing insurance policies at COFACE Brasil. The application is built on a traditional LAMP stack using PHP with ODBC database connections.

## Technology Stack

- **Language**: PHP (traditional procedural style, pre-framework)
- **Database**: ODBC connections to external databases (SisSeg system)
- **Frontend**: jQuery, CSS, HTML with some AJAX functionality
- **PDF Generation**: MPDF library
- **Excel Processing**: PhpSpreadsheet library
- **Email**: PHPMailer for SMTP functionality
- **JavaScript Libraries**: jQuery, TinyMCE editor, custom validation scripts

## Architecture

### Core Structure
- `index.php` - Main login page and application entry point
- `config.php` - Global configuration, session management, and utility functions
- `main.php` - Main application dashboard after login
- `src/role/` - Contains all business logic modules organized by functionality

### Key Modules
- **access/** - Authentication and user management
- **client/** - Client/importer management and credit operations
- **credit/** - Credit analysis, buyer management, and COFACE integration
- **policy/** - Insurance policy management and administration
- **executive/** - Executive workflow for proposal processing
- **dve/** - DVE (export credit declaration) processing
- **sinistro/** - Claims management
- **cessao/** - Policy assignment and transfer operations

### Database Architecture
- Uses ODBC connections to legacy systems
- Two main database connections: `$db` (main) and `$dbSisSeg` (SisSeg system)
- No ORM - direct SQL queries throughout the codebase
- Legacy `ereg_replace` functions (deprecated PHP functionality)

## Development Commands

This is a legacy PHP application without modern build tools. Development requires:

- **Web Server**: Apache/Nginx with PHP support
- **Database**: ODBC-compatible database system
- **Dependencies**: Install via Composer: `composer install`

Key dependencies:
- `mpdf/mpdf ^8.2` - PDF generation
- `phpoffice/phpspreadsheet ^3.8` - Excel file processing

## Configuration

### Database Configuration
- Database connections are configured in `src/role/rolePrefix.php`
- ODBC connection strings need to be configured for production environment
- Session configuration in `session_config.php`

### Environment Setup
- Application detects environment based on server hostname and request URI
- Different configurations for development, staging, and production
- File paths are dynamically determined based on environment

### Security Features
- Session security with httponly and secure cookie settings
- Password complexity validation with regex patterns
- SQL injection prevention through parameterized queries (where implemented)
- XSS protection through input sanitization

## Key Business Logic

### Credit Management Flow
1. Client creates inform (proposal request)
2. Executive processes and assigns to tariffer
3. Tariffer calculates premiums and creates proposal
4. Proposal sent to client for approval
5. Policy creation and management

### DVE Processing
- Export credit declarations with complex calculation logic
- Excel export functionality for financial reporting
- Integration with banking systems for payment processing

### Document Generation
- PDF generation for policies, proposals, and reports
- Email templates for automated notifications
- Multi-language support (Portuguese primary)

## Important Notes

- This is a legacy system with security vulnerabilities (deprecated PHP functions, potential SQL injection points)
- The codebase uses procedural PHP without modern frameworks
- Heavy reliance on session state management
- Complex business logic spread across multiple interconnected modules
- COFACE-specific business rules embedded throughout the application

## File Upload Handling
- File uploads processed in various modules (assinatura/, documents/)
- PDF and Excel file processing capabilities  
- Temporary file management in `src/download/` directory

When working with this codebase, prioritize security improvements and be cautious of the legacy code patterns that may not follow modern PHP best practices.