// Simple GET request handler
export async function GET(request) {
    return new Response(JSON.stringify({ message: "Hello from Next.js API!" }), {
      status: 200,
      headers: {
        'Content-Type': 'application/json',
      },
    });
  }
  
  // Example POST request handler
  export async function POST(request) {
    const data = await request.json();
    return new Response(JSON.stringify({ received: data }), {
      status: 201,
      headers: { 'Content-Type': 'application/json' },
    });
  }