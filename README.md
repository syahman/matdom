# Project MatDom

A professional, production-ready PHP application for testing DomPDF HTML-to-PDF conversion with a beautiful, modern interface.

## Features

### 🎨 Beautiful Modern Interface
- **Gradient Header Design** - Professional gradient background with glass effects
- **Tabbed Interface** - Clean separation between HTML source and PDF preview
- **Responsive Layout** - Works perfectly on desktop and mobile devices
- **Dark Code Editor** - Syntax-highlighted HTML editor with line numbers
- **Real-time Character Count** - Live feedback on content length

### ⚙️ Advanced PDF Configuration
- **Paper Sizes** - A4, A3, Letter, Legal support
- **Orientation Control** - Portrait and Landscape modes
- **Custom Margins** - Precise margin control in millimeters
- **DPI Settings** - 72, 96, 150, 300 DPI options
- **Advanced Options** - JavaScript, remote content, font subsetting

### 🚀 Professional Features
- **Error Handling** - Comprehensive error reporting and user feedback
- **Loading States** - Beautiful loading animations during PDF generation
- **Download Management** - Timestamped PDF downloads
- **Memory Optimization** - Efficient handling of large documents
- **Security** - Secure file handling and input validation

## Installation

### Requirements
- PHP 7.4 or higher
- Composer
- Web server (Apache/Nginx)

### Setup Instructions

1. **Clone or Download** the application files to your web server directory

2. **Install Dependencies**
   ```bash
   composer install
   ```

3. **Configure Web Server**
   - Ensure your web server can execute PHP files
   - Set appropriate permissions for the application directory
   - Configure virtual host if needed

4. **Access the Application**
   - Open your browser and navigate to the application URL
   - Example: `http://localhost/dompdf-ui-tester/`

## Usage

### Basic Usage
1. **Enter HTML Content** - Paste or type your HTML in the source editor
2. **Configure Settings** - Adjust paper size, orientation, margins, and other options
3. **Generate PDF** - Click "Generate PDF" to create the document
4. **Download** - Use the "Download" button to save the PDF file

### Advanced Configuration

#### Paper Settings
- **Paper Size**: Choose from A4, A3, Letter, or Legal
- **Orientation**: Select Portrait or Landscape mode

#### Margins
- Set custom margins in millimeters for all four sides
- Range: 0-50mm for precise control

#### Rendering Options
- **DPI Quality**: Choose from 72 (screen) to 300 (print) DPI
- **JavaScript**: Enable/disable JavaScript execution
- **Remote Content**: Allow/block external resources
- **Font Subsetting**: Optimize font embedding

### Sample HTML
The application includes a comprehensive sample HTML document that demonstrates:
- Modern CSS styling with gradients and shadows
- Responsive grid layouts
- Professional typography
- Styled tables with hover effects
- Advanced CSS features

## Technical Details

### Architecture
- **Frontend**: Modern HTML5, CSS3, and JavaScript
- **Backend**: PHP with DomPDF library
- **Styling**: Tailwind CSS for responsive design
- **Icons**: Heroicons for consistent iconography

### File Structure
```
dompdf-ui-tester/
├── index.php              # Main application interface
├── generate_pdf.php       # PDF generation handler
├── composer.json          # Dependency management
├── README.md             # Documentation
└── vendor/               # Composer dependencies
```

### Security Features
- Input validation and sanitization
- Secure file handling
- Error message sanitization
- Memory limit management
- Chroot directory restrictions

### Performance Optimizations
- Efficient memory usage for large documents
- Optimized CSS and JavaScript loading
- Compressed output and caching headers
- Font subsetting for smaller file sizes

## Customization

### Styling
The application uses Tailwind CSS classes and custom CSS for styling. You can customize:
- Color schemes and gradients
- Layout and spacing
- Typography and fonts
- Component styling

### Configuration
Default settings can be modified in the JavaScript section:
- Default paper size and orientation
- Margin presets
- DPI settings
- Feature toggles

### Extensions
The modular architecture allows for easy extensions:
- Additional paper sizes
- Custom CSS injection
- Watermark support
- Batch processing
- API integration

## Troubleshooting

### Common Issues

**PDF Generation Fails**
- Check PHP memory limit (recommended: 256M+)
- Verify DomPDF installation via Composer
- Ensure proper file permissions

**Styling Issues**
- Verify CSS syntax in HTML content
- Check for unsupported CSS properties
- Test with simplified HTML first

**Download Problems**
- Check browser popup blockers
- Verify server write permissions
- Test with different browsers

### Error Logging
The application includes comprehensive error logging:
- PHP errors are logged and displayed safely
- DomPDF-specific errors are captured
- User-friendly error messages

## Browser Compatibility
- **Chrome/Chromium**: Full support
- **Firefox**: Full support
- **Safari**: Full support
- **Edge**: Full support
- **Mobile Browsers**: Responsive design support

## License
This project is open source and available under the MIT License.

## Support
For issues, questions, or contributions, please refer to the project documentation or create an issue in the project repository.