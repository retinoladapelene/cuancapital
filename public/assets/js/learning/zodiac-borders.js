/**
 * CuanCapital Experience OS: Zodiac Avatar Borders
 * Generates the complex SVG hierarchy and CSS animations for Zodiac rarity borders.
 */

window.ZodiacBorders = (function () {
  const zodiacs = [
    {
      id: 'aries', name: 'ARIES', symbol: '♈', date: '21 Mar – 19 Apr',
      colors: { primary: '#e83020', secondary: '#ff8040', accent: '#ffd700', glow: 'rgba(232,48,32,0.6)' },
      animal: `
          <g transform="translate(85,85)">
            <path d="M-22,-30 Q-50,-55 -45,-75 Q-40,-90 -25,-80 Q-15,-72 -18,-55 Q-20,-42 -10,-32" fill="none" stroke="#ff8040" stroke-width="5" stroke-linecap="round"/>
            <path d="M22,-30 Q50,-55 45,-75 Q40,-90 25,-80 Q15,-72 18,-55 Q20,-42 10,-32" fill="none" stroke="#ff8040" stroke-width="5" stroke-linecap="round"/>
            <ellipse cx="0" cy="-18" rx="18" ry="15" fill="#c82010" opacity="0.9"/>
            <circle cx="-7" cy="-22" r="3" fill="#ffd700"/>
            <circle cx="7" cy="-22" r="3" fill="#ffd700"/>
            <circle cx="-7" cy="-22" r="1.5" fill="#1a0404"/>
            <circle cx="7" cy="-22" r="1.5" fill="#1a0404"/>
            <ellipse cx="0" cy="-10" rx="8" ry="6" fill="#a01808"/>
            <ellipse cx="-3" cy="-9" rx="2" ry="1.5" fill="#600"/>
            <ellipse cx="3" cy="-9" rx="2" ry="1.5" fill="#600"/>
            <path d="M-14,-5 Q-20,20 -10,40 Q0,50 10,40 Q20,20 14,-5" fill="#c82010" opacity="0.6"/>
            <path d="M-18,-30 Q-25,-45 -15,-50 Q-8,-45 -12,-35" fill="#ffd700" opacity="0.8"/>
            <path d="M-10,-33 Q-12,-50 -4,-52 Q2,-48 -2,-38" fill="#ff8040" opacity="0.9"/>
            <path d="M0,-34 Q2,-52 8,-52 Q12,-46 6,-38" fill="#ffd700" opacity="0.8"/>
            <path d="M10,-33 Q14,-50 20,-48 Q22,-42 14,-36" fill="#ff8040" opacity="0.9"/>
            <path d="M18,-30 Q26,-44 22,-50 Q16,-46 16,-36" fill="#ffd700" opacity="0.8"/>
          </g>`
    },
    {
      id: 'taurus', name: 'TAURUS', symbol: '♉', date: '20 Apr – 20 Mei',
      colors: { primary: '#2a8a50', secondary: '#60c880', accent: '#c87040', glow: 'rgba(42,138,80,0.6)' },
      animal: `
          <g transform="translate(85,85)">
            <path d="M-20,-28 Q-55,-50 -60,-35 Q-58,-20 -35,-22 Q-22,-23 -18,-28" fill="#60c880" stroke="#2a8a50" stroke-width="2" stroke-linejoin="round"/>
            <path d="M20,-28 Q55,-50 60,-35 Q58,-20 35,-22 Q22,-23 18,-28" fill="#60c880" stroke="#2a8a50" stroke-width="2" stroke-linejoin="round"/>
            <circle cx="-57" cy="-34" r="4" fill="#c87040"/>
            <circle cx="57" cy="-34" r="4" fill="#c87040"/>
            <ellipse cx="0" cy="-10" rx="22" ry="20" fill="#1a6030" opacity="0.95"/>
            <ellipse cx="0" cy="-20" rx="12" ry="8" fill="#0d3018" opacity="0.7"/>
            <circle cx="-10" cy="-15" r="5" fill="#c87040"/>
            <circle cx="10" cy="-15" r="5" fill="#c87040"/>
            <circle cx="-10" cy="-15" r="2.5" fill="#0a1a08"/>
            <circle cx="10" cy="-15" r="2.5" fill="#0a1a08"/>
            <circle cx="-9" cy="-16" r="1" fill="white" opacity="0.6"/>
            <circle cx="11" cy="-16" r="1" fill="white" opacity="0.6"/>
            <ellipse cx="0" cy="2" rx="12" ry="9" fill="#0f4020"/>
            <ellipse cx="-4" cy="3" rx="3" ry="2.5" fill="#082010"/>
            <ellipse cx="4" cy="3" rx="3" ry="2.5" fill="#082010"/>
            <path d="M-18,8 Q-25,35 -15,55 Q0,65 15,55 Q25,35 18,8Z" fill="#1a6030" opacity="0.5"/>
            <ellipse cx="0" cy="5" rx="6" ry="3" fill="none" stroke="#c87040" stroke-width="2"/>
          </g>`
    },
    {
      id: 'gemini', name: 'GEMINI', symbol: '♊', date: '21 Mei – 20 Jun',
      colors: { primary: '#d4c020', secondary: '#a080ff', accent: '#e0e0ff', glow: 'rgba(180,160,40,0.5)' },
      animal: `
          <g transform="translate(85,85)">
            <g transform="translate(-22, -10)">
              <ellipse cx="0" cy="-12" rx="14" ry="16" fill="#3a3060" opacity="0.95"/>
              <circle cx="-5" cy="-15" r="3" fill="#a080ff"/>
              <circle cx="5" cy="-15" r="3" fill="#a080ff"/>
              <circle cx="-5" cy="-15" r="1.5" fill="#1a1030"/>
              <circle cx="5" cy="-15" r="1.5" fill="#1a1030"/>
              <path d="M-5,-4 Q0,0 5,-4" fill="none" stroke="#d4c020" stroke-width="1.5" stroke-linecap="round"/>
              <path d="M-12,-24 Q-16,-35 -10,-38" stroke="#d4c020" stroke-width="2" fill="none"/>
              <path d="M-6,-28 Q-6,-40 0,-42" stroke="#d4c020" stroke-width="2" fill="none"/>
              <path d="M4,-27 Q8,-38 12,-36" stroke="#d4c020" stroke-width="2" fill="none"/>
            </g>
            <g transform="translate(22, -10) scale(-1,1)">
              <ellipse cx="0" cy="-12" rx="14" ry="16" fill="#2a4060" opacity="0.95"/>
              <circle cx="-5" cy="-15" r="3" fill="#60c0ff"/>
              <circle cx="5" cy="-15" r="3" fill="#60c0ff"/>
              <circle cx="-5" cy="-15" r="1.5" fill="#0a1828"/>
              <circle cx="5" cy="-15" r="1.5" fill="#0a1828"/>
              <path d="M-5,-4 Q0,0 5,-4" fill="none" stroke="#d4c020" stroke-width="1.5" stroke-linecap="round"/>
              <path d="M-12,-24 Q-16,-35 -10,-38" stroke="#60c0ff" stroke-width="2" fill="none"/>
              <path d="M-6,-28 Q-6,-40 0,-42" stroke="#60c0ff" stroke-width="2" fill="none"/>
              <path d="M4,-27 Q8,-38 12,-36" stroke="#60c0ff" stroke-width="2" fill="none"/>
            </g>
            <path d="M-8,5 Q0,12 8,5" stroke="#d4c020" stroke-width="1" fill="none" opacity="0.6"/>
            <circle cx="0" cy="-28" r="2" fill="#d4c020"/>
            <circle cx="0" cy="-34" r="1" fill="#a080ff"/>
            <path d="M-32,5 Q-38,35 -28,48 Q-20,55 -14,48 Q-10,35 -14,5Z" fill="#3a3060" opacity="0.4"/>
            <path d="M32,5 Q38,35 28,48 Q20,55 14,48 Q10,35 14,5Z" fill="#2a4060" opacity="0.4"/>
          </g>`
    },
    {
      id: 'cancer', name: 'CANCER', symbol: '♋', date: '21 Jun – 22 Jul',
      colors: { primary: '#5090d0', secondary: '#c0d8ff', accent: '#e0f0ff', glow: 'rgba(80,144,208,0.6)' },
      animal: `
          <g transform="translate(85,85)">
            <g stroke="#5090d0" stroke-width="3" stroke-linecap="round" fill="none" opacity="0.8">
              <path d="M-18,5 Q-45,0 -55,12"/>
              <path d="M-16,14 Q-45,16 -52,28"/>
              <path d="M-14,22 Q-40,30 -46,44"/>
              <path d="M18,5 Q45,0 55,12"/>
              <path d="M16,14 Q45,16 52,28"/>
              <path d="M14,22 Q40,30 46,44"/>
            </g>
            <g fill="#4080b0" stroke="#c0d8ff" stroke-width="1.5">
              <path d="M-20,-8 Q-55,-30 -65,-18 Q-60,-8 -48,-12 Q-40,-14 -36,-8 Q-50,5 -42,12 Q-34,18 -28,8 Q-22,2 -20,-8Z"/>
              <path d="M20,-8 Q55,-30 65,-18 Q60,-8 48,-12 Q40,-14 36,-8 Q50,5 42,12 Q34,18 28,8 Q22,2 20,-8Z"/>
            </g>
            <path d="M-65,-18 Q-72,-22 -70,-14 Q-68,-8 -62,-10" fill="#3070a0" stroke="#c0d8ff" stroke-width="1"/>
            <path d="M65,-18 Q72,-22 70,-14 Q68,-8 62,-10" fill="#3070a0" stroke="#c0d8ff" stroke-width="1"/>
            <ellipse cx="0" cy="10" rx="20" ry="18" fill="#3a78b0" opacity="0.95"/>
            <path d="M-18,8 Q0,-5 18,8" fill="none" stroke="#5090d0" stroke-width="1.5" opacity="0.7"/>
            <path d="M-16,16 Q0,8 16,16" fill="none" stroke="#5090d0" stroke-width="1" opacity="0.5"/>
            <line x1="-8" y1="-5" x2="-10" y2="-18" stroke="#c0d8ff" stroke-width="2.5"/>
            <line x1="8" y1="-5" x2="10" y2="-18" stroke="#c0d8ff" stroke-width="2.5"/>
            <circle cx="-10" cy="-20" r="5" fill="#c0d8ff"/>
            <circle cx="10" cy="-20" r="5" fill="#c0d8ff"/>
            <circle cx="-10" cy="-20" r="2.5" fill="#1a3050"/>
            <circle cx="10" cy="-20" r="2.5" fill="#1a3050"/>
            <ellipse cx="0" cy="10" rx="8" ry="6" fill="#c0d8ff" opacity="0.15"/>
          </g>`
    },
    {
      id: 'leo', name: 'LEO', symbol: '♌', date: '23 Jul – 22 Agt',
      colors: { primary: '#f0a020', secondary: '#ff6020', accent: '#ffe080', glow: 'rgba(240,160,32,0.7)' },
      animal: `
          <g transform="translate(85,85)">
            <g fill="#c06010" opacity="0.85">
              <polygon points="0,-62 6,-48 -6,-48" transform="rotate(0,0,0)"/>
              <polygon points="0,-62 6,-48 -6,-48" transform="rotate(30,0,0)"/>
              <polygon points="0,-62 6,-48 -6,-48" transform="rotate(60,0,0)"/>
              <polygon points="0,-62 6,-48 -6,-48" transform="rotate(90,0,0)"/>
              <polygon points="0,-62 6,-48 -6,-48" transform="rotate(120,0,0)"/>
              <polygon points="0,-62 6,-48 -6,-48" transform="rotate(150,0,0)"/>
              <polygon points="0,-62 6,-48 -6,-48" transform="rotate(180,0,0)"/>
              <polygon points="0,-62 6,-48 -6,-48" transform="rotate(210,0,0)"/>
              <polygon points="0,-62 6,-48 -6,-48" transform="rotate(240,0,0)"/>
              <polygon points="0,-62 6,-48 -6,-48" transform="rotate(270,0,0)"/>
              <polygon points="0,-62 6,-48 -6,-48" transform="rotate(300,0,0)"/>
              <polygon points="0,-62 6,-48 -6,-48" transform="rotate(330,0,0)"/>
            </g>
            <circle cx="0" cy="0" r="44" fill="#d07020" opacity="0.7"/>
            <ellipse cx="0" cy="-5" rx="28" ry="26" fill="#d08030" opacity="0.95"/>
            <polygon points="-25,-28 -32,-48 -12,-32" fill="#c06010"/>
            <polygon points="25,-28 32,-48 12,-32" fill="#c06010"/>
            <polygon points="-22,-30 -28,-44 -14,-34" fill="#e09050" opacity="0.6"/>
            <polygon points="22,-30 28,-44 14,-34" fill="#e09050" opacity="0.6"/>
            <path d="M-20,-20 Q-12,-28 -4,-22" fill="none" stroke="#8a4010" stroke-width="2.5"/>
            <path d="M20,-20 Q12,-28 4,-22" fill="none" stroke="#8a4010" stroke-width="2.5"/>
            <ellipse cx="-10" cy="-16" rx="6" ry="5" fill="#ffe080"/>
            <ellipse cx="10" cy="-16" rx="6" ry="5" fill="#ffe080"/>
            <ellipse cx="-10" cy="-16" rx="3" ry="4" fill="#1a0a00"/>
            <ellipse cx="10" cy="-16" rx="3" ry="4" fill="#1a0a00"/>
            <circle cx="-9" cy="-17" r="1" fill="white" opacity="0.7"/>
            <circle cx="11" cy="-17" r="1" fill="white" opacity="0.7"/>
            <path d="M-6,-6 Q0,-2 6,-6 Q4,2 0,4 Q-4,2 -6,-6Z" fill="#b04010"/>
            <g fill="#c06010" opacity="0.6">
              <circle cx="-16" cy="-2" r="1.5"/><circle cx="-20" cy="-5" r="1.5"/><circle cx="-16" cy="-8" r="1.5"/>
              <circle cx="16" cy="-2" r="1.5"/><circle cx="20" cy="-5" r="1.5"/><circle cx="16" cy="-8" r="1.5"/>
            </g>
            <path d="M-5,6 Q0,10 5,6" fill="none" stroke="#b04010" stroke-width="2" stroke-linecap="round"/>
            <path d="M-20,-30 L-16,-44 L-8,-36 L0,-48 L8,-36 L16,-44 L20,-30" fill="#f0a020" stroke="#ffe080" stroke-width="1.5" stroke-linejoin="round" opacity="0.9"/>
            <circle cx="0" cy="-48" r="3" fill="#ffe080"/>
            <circle cx="-16" cy="-44" r="2" fill="#ffe080"/>
            <circle cx="16" cy="-44" r="2" fill="#ffe080"/>
          </g>`
    },
    {
      id: 'virgo', name: 'VIRGO', symbol: '♍', date: '23 Agt – 22 Sep',
      colors: { primary: '#6aaa3a', secondary: '#a0d860', accent: '#d4c080', glow: 'rgba(100,170,60,0.5)' },
      animal: `
          <g transform="translate(85,85)">
            <g opacity="0.75">
              <path d="M-10,-20 Q-50,-55 -55,-30 Q-52,-10 -25,-12 Q-15,-13 -10,-20Z" fill="#6aaa3a" opacity="0.8"/>
              <path d="M-10,-8 Q-48,-5 -45,20 Q-38,38 -18,25 Q-12,18 -10,-8Z" fill="#4a8a2a" opacity="0.7"/>
              <path d="M10,-20 Q50,-55 55,-30 Q52,-10 25,-12 Q15,-13 10,-20Z" fill="#6aaa3a" opacity="0.8"/>
              <path d="M10,-8 Q48,-5 45,20 Q38,38 18,25 Q12,18 10,-8Z" fill="#4a8a2a" opacity="0.7"/>
              <path d="M-10,-20 Q-35,-40 -52,-28" stroke="#a0d860" stroke-width="1" fill="none" opacity="0.6"/>
              <path d="M-10,-8 Q-30,5 -44,22" stroke="#a0d860" stroke-width="1" fill="none" opacity="0.6"/>
              <path d="M10,-20 Q35,-40 52,-28" stroke="#a0d860" stroke-width="1" fill="none" opacity="0.6"/>
              <path d="M10,-8 Q30,5 44,22" stroke="#a0d860" stroke-width="1" fill="none" opacity="0.6"/>
            </g>
            <ellipse cx="0" cy="-18" rx="15" ry="17" fill="#4a8060" opacity="0.95"/>
            <path d="M-15,-30 Q-20,-48 -8,-52 Q0,-54 8,-52 Q20,-48 15,-30" fill="#2a5040" opacity="0.8"/>
            <ellipse cx="-5" cy="-22" rx="3.5" ry="3" fill="#a0d860"/>
            <ellipse cx="5" cy="-22" rx="3.5" ry="3" fill="#a0d860"/>
            <ellipse cx="-5" cy="-22" rx="2" ry="2.2" fill="#0a1808"/>
            <ellipse cx="5" cy="-22" rx="2" ry="2.2" fill="#0a1808"/>
            <path d="M-1.5,-14 Q0,-10 1.5,-14" fill="none" stroke="#2a5040" stroke-width="1.5"/>
            <path d="M-5,-7 Q0,-4 5,-7" fill="none" stroke="#a0d860" stroke-width="1.5" stroke-linecap="round"/>
            <g stroke="#d4c080" stroke-width="1.5" fill="none" opacity="0.9">
              <line x1="-30" y1="20" x2="-25" y2="-10"/>
              <line x1="-35" y1="22" x2="-28" y2="-8"/>
              <line x1="-40" y1="25" x2="-32" y2="-5"/>
              <line x1="30" y1="20" x2="25" y2="-10"/>
              <line x1="35" y1="22" x2="28" y2="-8"/>
              <line x1="40" y1="25" x2="32" y2="-5"/>
            </g>
            <g fill="#d4c080" opacity="0.8">
              <ellipse cx="-25" cy="-12" rx="4" ry="8" transform="rotate(-15,-25,-12)"/>
              <ellipse cx="-28" cy="-10" rx="4" ry="8" transform="rotate(-10,-28,-10)"/>
              <ellipse cx="-32" cy="-7" rx="4" ry="8" transform="rotate(-5,-32,-7)"/>
              <ellipse cx="25" cy="-12" rx="4" ry="8" transform="rotate(15,25,-12)"/>
              <ellipse cx="28" cy="-10" rx="4" ry="8" transform="rotate(10,28,-10)"/>
              <ellipse cx="32" cy="-7" rx="4" ry="8" transform="rotate(5,32,-7)"/>
            </g>
            <path d="M-12,-4 Q-18,20 -10,45 Q0,52 10,45 Q18,20 12,-4Z" fill="#3a7050" opacity="0.5"/>
            <line x1="-3" y1="-35" x2="-10" y2="-55" stroke="#a0d860" stroke-width="1.5"/>
            <line x1="3" y1="-35" x2="10" y2="-55" stroke="#a0d860" stroke-width="1.5"/>
            <circle cx="-10" cy="-55" r="2.5" fill="#a0d860"/>
            <circle cx="10" cy="-55" r="2.5" fill="#a0d860"/>
          </g>`
    },
    {
      id: 'scorpio', name: 'SCORPIO', symbol: '♏', date: '23 Okt – 21 Nov',
      colors: { primary: '#b01060', secondary: '#ff4090', accent: '#ff80c0', glow: 'rgba(176,16,96,0.7)' },
      animal: `
          <g transform="translate(85,85)">
            <path d="M20,20 Q45,30 50,10 Q55,-10 40,-20 Q28,-26 22,-20" fill="none" stroke="#b01060" stroke-width="6" stroke-linecap="round"/>
            <path d="M22,-20 Q18,-32 25,-36 Q32,-30 28,-20" fill="#ff4090"/>
            <path d="M25,-36 L28,-45" stroke="#ff80c0" stroke-width="2.5" stroke-linecap="round"/>
            <g stroke="#901040" stroke-width="2.5" stroke-linecap="round" fill="none">
              <path d="M-12,5 Q-35,-5 -40,5"/>
              <path d="M-10,12 Q-35,12 -38,22"/>
              <path d="M-8,20 Q-30,25 -32,38"/>
              <path d="M-6,28 Q-25,38 -24,50"/>
              <path d="M8,5 Q-5,5 -12,5"/>
            </g>
            <g stroke="#901040" stroke-width="2.5" stroke-linecap="round" fill="none" opacity="0.6">
              <path d="M10,12 Q30,18 32,28"/>
              <path d="M10,20 Q28,28 28,40"/>
            </g>
            <path d="M-14,-15 Q-45,-35 -52,-22 Q-55,-12 -44,-10 Q-36,-8 -30,-14 Q-22,-2 -16,-6 Q-12,-10 -14,-15Z" fill="#901040" stroke="#ff4090" stroke-width="1.5"/>
            <path d="M-52,-22 Q-62,-28 -58,-16 Q-54,-8 -46,-12" fill="#b01060" stroke="#ff4090" stroke-width="1"/>
            <path d="M14,-10 Q30,-22 35,-14 Q38,-8 30,-6 Q24,-4 20,-10Z" fill="#901040" stroke="#ff4090" stroke-width="1.5"/>
            <ellipse cx="0" cy="15" rx="16" ry="12" fill="#a01050" opacity="0.95"/>
            <ellipse cx="0" cy="3" rx="14" ry="10" fill="#b01060" opacity="0.95"/>
            <ellipse cx="0" cy="-7" rx="12" ry="9" fill="#c02070" opacity="0.95"/>
            <ellipse cx="-5" cy="-20" rx="10" ry="9" fill="#d03080" opacity="0.95"/>
            <circle cx="-9" cy="-23" r="3" fill="#ff4090"/>
            <circle cx="-2" cy="-23" r="3" fill="#ff4090"/>
            <circle cx="-9" cy="-23" r="1.5" fill="#200010"/>
            <circle cx="-2" cy="-23" r="1.5" fill="#200010"/>
            <circle cx="-9" cy="-23" r="3" fill="none" stroke="#ff80c0" stroke-width="1" opacity="0.7"/>
            <circle cx="-2" cy="-23" r="3" fill="none" stroke="#ff80c0" stroke-width="1" opacity="0.7"/>
          </g>`
    },
    {
      id: 'sagittarius', name: 'SAGITTARIUS', symbol: '♐', date: '22 Nov – 21 Des',
      colors: { primary: '#7040e0', secondary: '#40c0a0', accent: '#c0a0ff', glow: 'rgba(112,64,224,0.6)' },
      animal: `
          <g transform="translate(85,85)">
            <ellipse cx="15" cy="20" rx="28" ry="18" fill="#4a2ab0" opacity="0.85"/>
            <g fill="#3a1a90" stroke="#7040e0" stroke-width="1">
              <rect x="2" y="32" width="7" height="22" rx="3"/>
              <rect x="14" y="34" width="7" height="22" rx="3"/>
              <rect x="26" y="32" width="7" height="22" rx="3"/>
              <rect x="36" y="30" width="7" height="22" rx="3"/>
            </g>
            <g fill="#2a0a70">
              <ellipse cx="5" cy="54" rx="4" ry="2.5"/><ellipse cx="17" cy="56" rx="4" ry="2.5"/>
              <ellipse cx="29" cy="54" rx="4" ry="2.5"/><ellipse cx="39" cy="52" rx="4" ry="2.5"/>
            </g>
            <path d="M43,15 Q58,10 60,25 Q58,40 50,38" fill="none" stroke="#40c0a0" stroke-width="4" stroke-linecap="round"/>
            <path d="M-5,10 Q-12,-18 -8,-38 Q0,-44 8,-38 Q12,-18 5,10Z" fill="#5030b0" opacity="0.9"/>
            <line x1="-8" y1="-20" x2="-40" y2="-35" stroke="#5030b0" stroke-width="5" stroke-linecap="round"/>
            <line x1="8" y1="-18" x2="35" y2="-30" stroke="#5030b0" stroke-width="5" stroke-linecap="round"/>
            <path d="M-42,-20 Q-50,-35 -42,-50" fill="none" stroke="#40c0a0" stroke-width="3"/>
            <line x1="-42" y1="-20" x2="-42" y2="-50" stroke="#c0a0ff" stroke-width="1.5"/>
            <line x1="-42" y1="-35" x2="38" y2="-28" stroke="#c0a0ff" stroke-width="2"/>
            <polygon points="38,-28 30,-24 32,-31" fill="#c0a0ff"/>
            <path d="M-40,-35 Q-48,-30 -46,-38" fill="#7040e0" stroke="#c0a0ff" stroke-width="0.5"/>
            <ellipse cx="0" cy="-44" rx="12" ry="13" fill="#5030b0" opacity="0.95"/>
            <circle cx="-4" cy="-47" r="3" fill="#40c0a0"/><circle cx="4" cy="-47" r="3" fill="#c0a0ff"/>
            <circle cx="-4" cy="-47" r="1.5" fill="#100820"/><circle cx="4" cy="-47" r="1.5" fill="#100820"/>
            <path d="M-12,-52 Q-8,-65 0,-67 Q8,-65 12,-52" fill="#3a1a90" opacity="0.9"/>
            <polygon points="0,-56 1.5,-60 0,-58 -1.5,-60" fill="#c0a0ff"/>
          </g>`
    },
    {
      id: 'capricorn', name: 'CAPRICORN', symbol: '♑', date: '22 Des – 19 Jan',
      colors: { primary: '#208090', secondary: '#40d0c0', accent: '#80e8e0', glow: 'rgba(32,128,144,0.6)' },
      animal: `
          <g transform="translate(85,85)">
            <path d="M-12,25 Q-5,45 0,55 Q5,45 12,25" fill="#208090" opacity="0.8"/>
            <path d="M0,55 Q-20,70 -25,62 Q-15,52 0,55Z" fill="#187080"/>
            <path d="M0,55 Q20,70 25,62 Q15,52 0,55Z" fill="#187080"/>
            <path d="M-10,30 Q0,27 10,30" stroke="#40d0c0" stroke-width="1" fill="none" opacity="0.7"/>
            <path d="M-11,37 Q0,33 11,37" stroke="#40d0c0" stroke-width="1" fill="none" opacity="0.7"/>
            <path d="M-10,44 Q0,40 10,44" stroke="#40d0c0" stroke-width="1" fill="none" opacity="0.6"/>
            <ellipse cx="0" cy="8" rx="18" ry="20" fill="#186878" opacity="0.9"/>
            <ellipse cx="0" cy="-20" rx="14" ry="14" fill="#1a7080" opacity="0.95"/>
            <path d="M-6,0 Q0,12 6,0 Q4,18 0,22 Q-4,18 -6,0Z" fill="#145a68" opacity="0.8"/>
            <ellipse cx="-16" cy="-20" rx="5" ry="9" fill="#186878" transform="rotate(-20,-16,-20)"/>
            <ellipse cx="16" cy="-20" rx="5" ry="9" fill="#186878" transform="rotate(20,16,-20)"/>
            <path d="M-8,-30 Q-20,-55 -10,-65 Q-2,-58 -5,-45 Q-6,-36 -8,-30" fill="#40d0c0" opacity="0.9"/>
            <path d="M8,-30 Q20,-55 10,-65 Q2,-58 5,-45 Q6,-36 8,-30" fill="#40d0c0" opacity="0.9"/>
            <ellipse cx="-5" cy="-23" rx="4" ry="3.5" fill="#80e8e0"/>
            <ellipse cx="5" cy="-23" rx="4" ry="3.5" fill="#80e8e0"/>
            <ellipse cx="-5" cy="-23" rx="1.5" ry="3" fill="#081820"/>
            <ellipse cx="5" cy="-23" rx="1.5" ry="3" fill="#081820"/>
            <ellipse cx="0" cy="-12" rx="7" ry="5" fill="#145a68"/>
            <ellipse cx="-2" cy="-11" rx="2" ry="1.5" fill="#0a3840"/>
            <ellipse cx="2" cy="-11" rx="2" ry="1.5" fill="#0a3840"/>
            <path d="M-14,20 Q-22,35 -18,48 Q-14,50 -12,48 Q-12,35 -12,20Z" fill="#186878" opacity="0.8"/>
            <path d="M14,20 Q22,35 18,48 Q14,50 12,48 Q12,35 12,20Z" fill="#186878" opacity="0.8"/>
          </g>`
    },
    {
      id: 'aquarius', name: 'AQUARIUS', symbol: '♒', date: '20 Jan – 18 Feb',
      colors: { primary: '#1060d0', secondary: '#40c0ff', accent: '#80e0ff', glow: 'rgba(16,96,208,0.7)' },
      animal: `
          <g transform="translate(85,85)">
            <g opacity="0.9">
              <path d="M-30,-45 Q-15,-35 0,-40 Q15,-45 30,-35 Q45,-25 50,-10 Q40,-15 30,-25 Q15,-35 0,-30 Q-15,-25 -30,-35 Q-45,-45 -50,-30 Q-40,-20 -30,-30Z" fill="#1060d0" opacity="0.6"/>
              <path d="M-45,5 Q-30,15 -10,8 Q10,1 30,12 Q50,22 55,38 Q40,30 25,20 Q10,10 -10,18 Q-30,26 -45,18 Q-60,10 -58,25 Q-48,22 -45,5Z" fill="#40c0ff" opacity="0.5"/>
              <path d="M-40,35 Q-20,45 0,38 Q20,31 40,42 Q55,52 52,62 Q38,55 20,48 Q0,41 -20,48 Q-40,55 -50,48 Q-45,40 -40,35Z" fill="#1060d0" opacity="0.4"/>
            </g>
            <g fill="#80e0ff" opacity="0.8">
              <polygon points="15,-30 17,-24 23,-24 18,-20 20,-14 15,-18 10,-14 12,-20 7,-24 13,-24"/>
              <polygon points="-20,5 -18.5,10 -14,10 -17.5,13 -16,18 -20,15 -24,18 -22.5,13 -26,10 -21.5,10"/>
              <polygon points="35,20 36.5,25 41,25 37.5,28 39,33 35,30 31,33 32.5,28 29,25 33.5,25"/>
            </g>
            <ellipse cx="-15" cy="-50" rx="11" ry="12" fill="#1060d0" opacity="0.95"/>
            <circle cx="-19" cy="-53" r="2.5" fill="#80e0ff"/><circle cx="-11" cy="-53" r="2.5" fill="#80e0ff"/>
            <circle cx="-19" cy="-53" r="1.2" fill="#040820"/><circle cx="-11" cy="-53" r="1.2" fill="#040820"/>
            <path d="M-26,-58 Q-22,-68 -15,-70 Q-8,-68 -4,-58 Q-10,-55 -15,-60 Q-20,-55 -26,-58Z" fill="#0840a0"/>
            <path d="M-22,-40 Q-28,-20 -22,5 Q-15,10 -8,5 Q-2,-20 -8,-40Z" fill="#0a50b0" opacity="0.85"/>
            <path d="M-22,-30 Q-40,-25 -45,-15" stroke="#1060d0" stroke-width="5" stroke-linecap="round" fill="none"/>
            <path d="M-48,-18 Q-60,-20 -62,-8 Q-60,5 -48,8 Q-38,10 -36,-2 Q-34,-14 -48,-18Z" fill="#0840a0" stroke="#40c0ff" stroke-width="1.5"/>
            <path d="M-60,-5 Q-65,5 -58,15 Q-55,8 -60,-5Z" fill="#40c0ff" opacity="0.8"/>
            <path d="M-58,10 Q-62,22 -55,30" stroke="#80e0ff" stroke-width="2" stroke-linecap="round" fill="none"/>
            <path d="M-10,-35 L-5,-20 L-15,-18 L-5,0" stroke="#80e0ff" stroke-width="2" fill="none" stroke-linecap="round"/>
          </g>`
    }
  ];

  function buildRing(z) {
    const { primary, secondary, accent } = z.colors;
    switch (z.id) {
      case 'aries':
        return `
                <svg class="bg-ring" viewBox="0 0 170 170" fill="none">
                    <circle cx="85" cy="85" r="82" stroke="${primary}" stroke-width="3" fill="none"/>
                    <circle cx="85" cy="85" r="77" stroke="${accent}" stroke-width="0.5" fill="none" opacity="0.4"/>
                    <circle cx="85" cy="85" r="74" stroke="${secondary}" stroke-width="1" stroke-dasharray="4 6" fill="none" opacity="0.5"/>
                </svg>
                <svg class="zodiac-ring" style="position:absolute;inset:-12px;width:calc(100% + 24px);height:calc(100% + 24px);animation:spin 16s linear infinite;" viewBox="0 0 194 194" fill="none">
                    <g transform="translate(97,97)">
                        ${[0, 45, 90, 135, 180, 225, 270, 315].map(a => `<polygon points="0,-95 3,-88 -3,-88" transform="rotate(${a})" fill="${accent}"/>`).join('')}
                        <circle r="93" stroke="${primary}" stroke-width="1.5" fill="none" stroke-dasharray="6 8"/>
                    </g>
                </svg>`;
      case 'taurus':
        return `
                <svg class="bg-ring" viewBox="0 0 170 170" fill="none">
                    <circle cx="85" cy="85" r="82" stroke="${primary}" stroke-width="3" fill="none"/>
                    <circle cx="85" cy="85" r="76" stroke="${secondary}" stroke-width="0.8" fill="none" opacity="0.5"/>
                    <circle cx="85" cy="85" r="73" stroke="${accent}" stroke-width="0.5" stroke-dasharray="3 5" fill="none" opacity="0.4"/>
                </svg>
                <svg class="zodiac-ring" style="position:absolute;inset:-10px;width:calc(100% + 20px);height:calc(100% + 20px);animation:spin 22s linear infinite reverse;" viewBox="0 0 190 190" fill="none">
                    <g transform="translate(95,95)">
                        ${[0, 60, 120, 180, 240, 300].map(a => `<ellipse cx="0" cy="-91" rx="5" ry="8" transform="rotate(${a})" fill="${accent}" opacity="0.8"/>`).join('')}
                        <circle r="91" stroke="${secondary}" stroke-width="1" fill="none" stroke-dasharray="8 10"/>
                    </g>
                </svg>`;
      case 'gemini':
        return `
                <svg class="bg-ring" viewBox="0 0 170 170" fill="none">
                    <circle cx="85" cy="85" r="82" stroke="${primary}" stroke-width="2" fill="none"/>
                    <circle cx="85" cy="85" r="82" stroke="${secondary}" stroke-width="2" fill="none" stroke-dasharray="50 50" opacity="0.7"/>
                    <circle cx="85" cy="85" r="76" stroke="${accent}" stroke-width="0.5" fill="none" opacity="0.3"/>
                </svg>
                <svg class="zodiac-ring" style="position:absolute;inset:-10px;width:calc(100% + 20px);height:calc(100% + 20px);animation:spin 18s linear infinite;" viewBox="0 0 190 190" fill="none">
                    <g transform="translate(95,95)">
                        <circle r="91" stroke="${primary}" stroke-width="1" fill="none" stroke-dasharray="4 4"/>
                        ${[0, 30, 60, 90, 120, 150, 180, 210, 240, 270, 300, 330].map(a => `<circle cx="${Math.cos((a - 90) * Math.PI / 180) * 91}" cy="${Math.sin((a - 90) * Math.PI / 180) * 91}" r="2.5" fill="${a % 60 === 0 ? primary : secondary}"/>`).join('')}
                    </g>
                </svg>`;
      case 'cancer':
        return `
                <svg class="bg-ring" viewBox="0 0 170 170" fill="none">
                    <circle cx="85" cy="85" r="82" stroke="${primary}" stroke-width="2.5" fill="none"/>
                    <circle cx="85" cy="85" r="77" stroke="${secondary}" stroke-width="0.5" fill="none" opacity="0.5"/>
                    <circle cx="85" cy="85" r="72" stroke="${accent}" stroke-width="0.3" stroke-dasharray="2 4" fill="none" opacity="0.3"/>
                </svg>
                <svg class="zodiac-ring" style="position:absolute;inset:-10px;width:calc(100% + 20px);height:calc(100% + 20px);animation:spin 28s linear infinite;" viewBox="0 0 190 190" fill="none">
                    <g transform="translate(95,95)">
                        ${Array.from({ length: 16 }, (_, i) => { const a = i * 22.5 - 90; const x = Math.cos(a * Math.PI / 180) * 91; const y = Math.sin(a * Math.PI / 180) * 91; return `<circle cx="${x.toFixed(1)}" cy="${y.toFixed(1)}" r="${i % 4 === 0 ? 3.5 : 2}" fill="${i % 4 === 0 ? accent : secondary}" opacity="${i % 2 === 0 ? 1 : 0.5}"/>`; }).join('')}
                    </g>
                </svg>`;
      case 'leo':
        return `
                <svg class="bg-ring" viewBox="0 0 170 170" fill="none">
                    <circle cx="85" cy="85" r="82" stroke="${primary}" stroke-width="3.5" fill="none"/>
                    <circle cx="85" cy="85" r="76" stroke="${accent}" stroke-width="1" fill="none" opacity="0.5"/>
                </svg>
                <svg class="zodiac-ring" style="position:absolute;inset:-12px;width:calc(100% + 24px);height:calc(100% + 24px);animation:spin 12s linear infinite;" viewBox="0 0 194 194" fill="none">
                    <g transform="translate(97,97)">
                        ${[0, 30, 60, 90, 120, 150, 180, 210, 240, 270, 300, 330].map(a => `<polygon points="0,-95 4,-86 -4,-86" transform="rotate(${a})" fill="${a % 90 === 0 ? accent : primary}" opacity="${a % 90 === 0 ? 1 : 0.6}"/>`).join('')}
                        <circle r="93" stroke="${primary}" stroke-width="2" fill="none"/>
                    </g>
                </svg>`;
      case 'virgo':
        return `
                <svg class="bg-ring" viewBox="0 0 170 170" fill="none">
                    <circle cx="85" cy="85" r="82" stroke="${primary}" stroke-width="2.5" fill="none"/>
                    <circle cx="85" cy="85" r="77" stroke="${accent}" stroke-width="0.5" stroke-dasharray="5 3" fill="none" opacity="0.5"/>
                </svg>
                <svg class="zodiac-ring" style="position:absolute;inset:-10px;width:calc(100% + 20px);height:calc(100% + 20px);animation:spin 20s linear infinite reverse;" viewBox="0 0 190 190" fill="none">
                    <g transform="translate(95,95)">
                        ${Array.from({ length: 24 }, (_, i) => { const a = i * 15 - 90; const x = Math.cos(a * Math.PI / 180) * 91; const y = Math.sin(a * Math.PI / 180) * 91; return `<circle cx="${x.toFixed(1)}" cy="${y.toFixed(1)}" r="${i % 6 === 0 ? 3 : 1.5}" fill="${accent}" opacity="${i % 3 === 0 ? 0.9 : 0.4}"/>`; }).join('')}
                    </g>
                </svg>`;
      case 'scorpio':
        return `
                <svg class="bg-ring" viewBox="0 0 170 170" fill="none">
                    <circle cx="85" cy="85" r="82" stroke="${primary}" stroke-width="3" fill="none"/>
                    <circle cx="85" cy="85" r="76" stroke="${secondary}" stroke-width="1" fill="none" opacity="0.4"/>
                </svg>
                <svg class="zodiac-ring" style="position:absolute;inset:-10px;width:calc(100% + 20px);height:calc(100% + 20px);animation:spin 14s linear infinite;" viewBox="0 0 190 190" fill="none">
                    <g transform="translate(95,95)">
                        ${[0, 45, 90, 135, 180, 225, 270, 315].map(a => `<path d="M0,-93 Q5,-86 0,-80 Q-5,-86 0,-93" transform="rotate(${a})" fill="${accent}"/>`).join('')}
                        <circle r="91" stroke="${secondary}" stroke-width="1.5" fill="none" stroke-dasharray="6 4"/>
                    </g>
                </svg>`;
      case 'sagittarius':
        return `
                <svg class="bg-ring" viewBox="0 0 170 170" fill="none">
                    <circle cx="85" cy="85" r="82" stroke="${primary}" stroke-width="2.5" fill="none"/>
                    <circle cx="85" cy="85" r="77" stroke="${secondary}" stroke-width="0.5" stroke-dasharray="8 5" fill="none" opacity="0.5"/>
                </svg>
                <svg class="zodiac-ring" style="position:absolute;inset:-12px;width:calc(100% + 24px);height:calc(100% + 24px);animation:spin 20s linear infinite;" viewBox="0 0 194 194" fill="none">
                    <g transform="translate(97,97)">
                        ${[0, 60, 120, 180, 240, 300].map(a => `
                          <line x1="0" y1="-93" x2="0" y2="-82" transform="rotate(${a})" stroke="${accent}" stroke-width="3"/>
                          <polygon points="0,-93 4,-85 -4,-85" transform="rotate(${a})" fill="${secondary}"/>
                        `).join('')}
                        <circle r="91" stroke="${primary}" stroke-width="1.5" fill="none"/>
                    </g>
                </svg>`;
      case 'capricorn':
        return `
                <svg class="bg-ring" viewBox="0 0 170 170" fill="none">
                    <circle cx="85" cy="85" r="82" stroke="${primary}" stroke-width="2.5" fill="none"/>
                    <circle cx="85" cy="85" r="77" stroke="${secondary}" stroke-width="0.8" fill="none" opacity="0.4"/>
                    <circle cx="85" cy="85" r="73" stroke="${accent}" stroke-width="0.4" stroke-dasharray="3 6" fill="none" opacity="0.3"/>
                </svg>
                <svg class="zodiac-ring" style="position:absolute;inset:-10px;width:calc(100% + 20px);height:calc(100% + 20px);animation:spin 25s linear infinite reverse;" viewBox="0 0 190 190" fill="none">
                    <g transform="translate(95,95)">
                        ${[0, 45, 90, 135, 180, 225, 270, 315].map(a => `<ellipse cx="0" cy="-91" rx="4" ry="6" transform="rotate(${a})" fill="${secondary}" opacity="0.8"/>`).join('')}
                        <circle r="91" stroke="${primary}" stroke-width="1.5" fill="none" stroke-dasharray="10 6"/>
                    </g>
                </svg>`;
      case 'aquarius':
        return `
                <svg class="bg-ring" viewBox="0 0 170 170" fill="none">
                    <circle cx="85" cy="85" r="82" stroke="${primary}" stroke-width="3" fill="none"/>
                    <circle cx="85" cy="85" r="77" stroke="${secondary}" stroke-width="0.8" fill="none" opacity="0.5"/>
                </svg>
                <svg class="zodiac-ring" style="position:absolute;inset:-10px;width:calc(100% + 20px);height:calc(100% + 20px);animation:spin 16s linear infinite;" viewBox="0 0 190 190" fill="none">
                    <g transform="translate(95,95)">
                        ${Array.from({ length: 12 }, (_, i) => { const a = i * 30 - 90; const x = Math.cos(a * Math.PI / 180) * 91; const y = Math.sin(a * Math.PI / 180) * 91; return `<polygon points="${x.toFixed(1)},${y.toFixed(1)} ${(x + 5).toFixed(1)},${(y - 7).toFixed(1)} ${(x - 5).toFixed(1)},${(y - 7).toFixed(1)}" fill="${i % 3 === 0 ? accent : secondary}" transform="rotate(${i * 30},${x.toFixed(1)},${y.toFixed(1)})"/>`; }).join('')}
                        <circle r="91" stroke="${primary}" stroke-width="1" fill="none" stroke-dasharray="5 5"/>
                    </g>
                </svg>`;
      default:
        return '';
    }
  }

  /**
   * Renders the Zodiac HTML directly around an existing avatar image string.
   * Use scale to adjust it relative to its 170px base design size.
   * For an 88px container, scale should be ~0.52 (since 170 * 0.52 = 88.4px).
   * But it's easier to scale via CSS transform!
   */
  function render(cssClass, scalePxContainerSize) {
    // Find matching zodiac definition
    const id = cssClass.replace('zodiac-', '');
    const z = zodiacs.find(z => z.id === id);

    if (!z) return ''; // Fallback

    const baseSize = 170;
    const scale = scalePxContainerSize / baseSize;

    const { glow } = z.colors;

    return `
        <div class="zodiac-aw ${z.id}" style="transform: translate(-50%, -50%) scale(${scale});">
            <!-- Glow -->
            <div class="zodiac-core-shadow" style="box-shadow:0 0 30px 10px ${glow}; animation:pulse-glow-${z.id} 3s ease-in-out infinite;"></div>
            <!-- BG rings -->
            ${buildRing(z)}
            <!-- Inner dark core -->
            <div class="core absolute" style="width:108px; height:108px; z-index:4; border-radius:50%; pointer-events:none;">
                <!-- User avatar lies directly under this in the component hierarchy -->
                <!-- Animal SVG -->
                <svg viewBox="0 0 170 170" style="position:absolute;top:-31px;left:-31px;width:170px;height:170px;z-index:5;pointer-events:none;" fill="none">
                    ${z.animal}
                </svg>
            </div>
        </div>
        `;
  }

  return {
    renderHTML: render,
    getMeta: function (cssClass) {
      const id = cssClass.replace('zodiac-', '');
      return zodiacs.find(z => z.id === id);
    }
  };
})();
