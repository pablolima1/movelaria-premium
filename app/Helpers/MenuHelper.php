<?php

namespace App\Helpers;

class MenuHelper
{
    public static function getMenuGroups(): array
    {
        return [
            [
                'title' => 'Menu',
                'items' => [
                    [
                        'icon' => 'dashboard',
                        'name' => 'Home',
                        'path' => '/home',
                    ],
                    [
                        'icon' => 'clientes',
                        'name' => 'Clientes',
                        'path' => '/clientes',
                    ],
                    [
                        'icon' => 'tipos-projeto',
                        'name' => 'Tipos de projeto',
                        'path' => '/tipos-projeto',
                    ],
                    [
                        'icon' => 'aliquotas-imposto',
                        'name' => 'Alíquotas de imposto',
                        'path' => '/aliquotas-imposto',
                    ],
                ],
            ],
        ];
    }

    public static function getIconSvg(string $iconName): string
    {
        return match ($iconName) {
            'dashboard' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M5.5 3.25C4.25736 3.25 3.25 4.25736 3.25 5.5V8.99998C3.25 10.2426 4.25736 11.25 5.5 11.25H9C10.2426 11.25 11.25 10.2426 11.25 8.99998V5.5C11.25 4.25736 10.2426 3.25 9 3.25H5.5ZM4.75 5.5C4.75 5.08579 5.08579 4.75 5.5 4.75H9C9.41421 4.75 9.75 5.08579 9.75 5.5V8.99998C9.75 9.41419 9.41421 9.74998 9 9.74998H5.5C5.08579 9.74998 4.75 9.41419 4.75 8.99998V5.5ZM5.5 12.75C4.25736 12.75 3.25 13.7574 3.25 15V18.5C3.25 19.7426 4.25736 20.75 5.5 20.75H9C10.2426 20.75 11.25 19.7427 11.25 18.5V15C11.25 13.7574 10.2426 12.75 9 12.75H5.5ZM4.75 15C4.75 14.5858 5.08579 14.25 5.5 14.25H9C9.41421 14.25 9.75 14.5858 9.75 15V18.5C9.75 18.9142 9.41421 19.25 9 19.25H5.5C5.08579 19.25 4.75 18.9142 4.75 18.5V15ZM12.75 5.5C12.75 4.25736 13.7574 3.25 15 3.25H18.5C19.7426 3.25 20.75 4.25736 20.75 5.5V8.99998C20.75 10.2426 19.7426 11.25 18.5 11.25H15C13.7574 11.25 12.75 10.2426 12.75 8.99998V5.5ZM15 4.75C14.5858 4.75 14.25 5.08579 14.25 5.5V8.99998C14.25 9.41419 14.5858 9.74998 15 9.74998H18.5C18.9142 9.74998 19.25 9.41419 19.25 8.99998V5.5C19.25 5.08579 18.9142 4.75 18.5 4.75H15ZM15 12.75C13.7574 12.75 12.75 13.7574 12.75 15V18.5C12.75 19.7426 13.7574 20.75 15 20.75H18.5C19.7426 20.75 20.75 19.7427 20.75 18.5V15C20.75 13.7574 19.7426 12.75 18.5 12.75H15ZM14.25 15C14.25 14.5858 14.5858 14.25 15 14.25H18.5C18.9142 14.25 19.25 14.5858 19.25 15V18.5C19.25 18.9142 18.9142 19.25 18.5 19.25H15C14.5858 19.25 14.25 18.9142 14.25 18.5V15Z" fill="currentColor"></path></svg>',
            'clientes' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M8.25 6.5C8.25 4.42893 9.92893 2.75 12 2.75C14.0711 2.75 15.75 4.42893 15.75 6.5C15.75 8.57107 14.0711 10.25 12 10.25C9.92893 10.25 8.25 8.57107 8.25 6.5ZM12 4.25C10.7574 4.25 9.75 5.25736 9.75 6.5C9.75 7.74264 10.7574 8.75 12 8.75C13.2426 8.75 14.25 7.74264 14.25 6.5C14.25 5.25736 13.2426 4.25 12 4.25ZM6.25 19C6.25 15.8244 8.82436 13.25 12 13.25C15.1756 13.25 17.75 15.8244 17.75 19V19.25H6.25V19ZM12 14.75C9.65279 14.75 7.75 16.6528 7.75 19V19.25H16.25V19C16.25 16.6528 14.3472 14.75 12 14.75Z" fill="currentColor"></path></svg>',
            'tipos-projeto' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M4 4.75h16M5.75 4.75v14.5h12.5V4.75M5.75 12h12.5M10 8.25h4M10 15.75h4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path><path d="M7.25 19.25v1M16.75 19.25v1" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"></path></svg>',
            'aliquotas-imposto' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M18 6L6 18" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"></path><circle cx="7.5" cy="7.5" r="2.75" stroke="currentColor" stroke-width="1.5"></circle><circle cx="16.5" cy="16.5" r="2.75" stroke="currentColor" stroke-width="1.5"></circle></svg>',
            default => '',
        };
    }
}
