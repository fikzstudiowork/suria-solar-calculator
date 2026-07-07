/** Build WhatsApp click-to-chat URL (api.whatsapp.com format). */
export function buildWhatsAppLink(number: string, text?: string): string {
  const digits = number.replace(/\D/g, "");
  const params = new URLSearchParams({
    phone: digits,
    type: "phone_number",
    app_absent: "0",
  });
  if (text) params.set("text", text);
  return `https://api.whatsapp.com/send/?${params.toString()}`;
}
