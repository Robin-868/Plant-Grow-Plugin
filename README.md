# Plant Grow Plugin

A WordPress plugin that displays a scroll-triggered plant growth widget. As users scroll down your page, the plant grows through 14 different stages, encouraging them to read through your content.

## Features

- **14 Growth Stages**: The plant progresses through 14 stages as users scroll
- **Progress Bar**: Visual indicator showing scroll progress percentage
- **Never Shrinks**: Once a growth stage is reached, it stays even if users scroll back up
- **Fully Customizable**: 
  - Position (bottom/left offsets)
  - Widget size
  - Progress bar color
  - Text color and size
  - Mobile background color
- **Image Support**: 
  - Default images included in plugin directory
  - Option to use WordPress Media Library for custom images
- **Mobile Responsive**: Automatically adjusts size and appearance on mobile devices

## Installation

1. Upload the `plant-grow-plugin` folder to `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to Settings > Plant Grow to configure the plugin

## Setup Images

The plugin includes default plant stage images in `assets/images/`. If you want to use custom images:

1. Go to Settings > Plant Grow
2. Check "Use WordPress Media Library"
3. Upload custom images for each of the 14 stages using the image upload buttons
4. Save settings

**Note**: If you haven't copied the default images yet, copy all `plant-stage-*.png` files from the `Plant stages` folder to `assets/images/` folder in the plugin directory.

## Configuration

Navigate to **Settings > Plant Grow** to configure:

### General Settings
- **Enable Widget**: Toggle to show/hide the widget on frontend

### Position Settings
- **Bottom Offset**: Distance from bottom of screen (in pixels)
- **Left Offset**: Distance from left edge (can be negative to go off-screen)
- **Widget Width**: Overall width of the widget (in pixels)

### Appearance Settings
- **Progress Bar Color**: Color of the progress bar
- **Progress Text Color**: Color of the percentage text
- **Progress Text Size**: Font size of the percentage text
- **Mobile Background Color**: Background color shown on mobile devices

### Plant Stage Images
- **Use WordPress Media Library**: Switch between plugin default images and uploaded images
- **Stage 1-14 Images**: Upload custom images for each growth stage

## How It Works

The plugin tracks scroll progress and displays different plant stages:
- **0-7%**: Stage 1 (Seeds)
- **8-14%**: Stage 2
- **15-21%**: Stage 3
- **22-28%**: Stage 4
- **29-35%**: Stage 5
- **36-42%**: Stage 6
- **43-49%**: Stage 7
- **50-56%**: Stage 8
- **57-63%**: Stage 9
- **64-70%**: Stage 10
- **71-77%**: Stage 11
- **78-84%**: Stage 12
- **85-91%**: Stage 13
- **92-100%**: Stage 14 (Fully grown/Flower)

## File Structure

```
plant-grow-plugin/
├── plant-grow-plugin.php          # Main plugin file
├── includes/
│   ├── class-plant-grow-admin.php # Admin settings
│   └── class-plant-grow-frontend.php # Frontend display
├── assets/
│   ├── css/
│   │   └── plant-grow.css         # Widget styles
│   ├── js/
│   │   ├── plant-grow.js          # Frontend JavaScript
│   │   └── admin.js               # Admin JavaScript
│   └── images/
│       └── plant-stage-*.png      # Default plant images (14 files)
└── README.md
```

## Requirements

- WordPress 5.0 or higher
- PHP 7.0 or higher

## Support

For issues, questions, or feature requests, please contact the plugin author.

## License

GPL v2 or later

