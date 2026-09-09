import csv

data = [
    # Electrical
    ("Fan is running slowly", "Electrical"),
    ("Fan speed is very low", "Electrical"),
    ("The ceiling fan is not working properly", "Electrical"),
    ("Light is flickering", "Electrical"),
    ("Room light keeps turning on and off", "Electrical"),
    ("The light is not working", "Electrical"),
    ("Switch is not working", "Electrical"),
    ("The switch is damaged", "Electrical"),
    ("Switch board is damaged", "Electrical"),
    ("Socket has no power", "Electrical"),
    ("There is no power in the wall socket", "Electrical"),
    ("Plug point is loose", "Electrical"),
    ("The plug point is damaged", "Electrical"),
    ("Heater is not working", "Electrical"),
    ("The room heater is not functioning", "Electrical"),
    ("There is low voltage in my room", "Electrical"),
    ("The voltage in my room is very low", "Electrical"),
    ("Electrical power is unstable in my room", "Electrical"),

    # Plumbing & Drainage
    ("Water is leaking from the tap", "Plumbing & Drainage"),
    ("There is a water leak in my bathroom", "Plumbing & Drainage"),
    ("The bathroom tap is leaking", "Plumbing & Drainage"),
    ("Water is continuously dripping from the tap", "Plumbing & Drainage"),
    ("The bathroom tap has low water flow", "Plumbing & Drainage"),
    ("Water comes very slowly from the tap", "Plumbing & Drainage"),
    ("The water pressure from my tap is very low", "Plumbing & Drainage"),
    ("There is very little water coming from the bathroom tap", "Plumbing & Drainage"),
    ("The bathroom drain is clogged", "Plumbing & Drainage"),
    ("Water is not draining properly", "Plumbing & Drainage"),
    ("Water is collecting near the bathroom drain", "Plumbing & Drainage"),
    ("The drain is blocked", "Plumbing & Drainage"),
    ("The bathroom drain is overflowing", "Plumbing & Drainage"),

    # Water Supply
    ("There is no water supply in my room", "Water Supply"),
    ("Water supply is not available", "Water Supply"),
    ("There is no water coming in my bathroom", "Water Supply"),
    ("The water supply has stopped", "Water Supply"),
    ("I am not getting water in my bathroom", "Water Supply"),
    ("There is no water supply right now", "Water Supply"),
    ("Water is not available in my room", "Water Supply"),

    # Drinking Water
    ("Drinking water is not available", "Drinking Water"),
    ("There is no drinking water", "Drinking Water"),
    ("The drinking water is dirty", "Drinking Water"),
    ("The drinking water looks unclean", "Drinking Water"),
    ("The drinking water is not suitable for drinking", "Drinking Water"),
    ("There is no cold drinking water", "Drinking Water"),
    ("Cold drinking water is not available", "Drinking Water"),
    ("There is no hot drinking water", "Drinking Water"),
    ("Hot drinking water is not available", "Drinking Water"),
    ("The drinking water filter has algae", "Drinking Water"),
    ("There is algae inside the drinking water filter", "Drinking Water"),
    ("The drinking water filter knob is broken", "Drinking Water"),
    ("The filter tap is not working", "Drinking Water"),
    ("The drinking water filter tap is damaged", "Drinking Water"),
    ("The drinking water filter is not working properly", "Drinking Water"),

    # Cleaning & Hygiene
    ("The bathroom is not clean", "Cleaning & Hygiene"),
    ("The bathroom needs cleaning", "Cleaning & Hygiene"),
    ("My room is dirty", "Cleaning & Hygiene"),
    ("The room has not been cleaned properly", "Cleaning & Hygiene"),
    ("The room has not been mopped", "Cleaning & Hygiene"),
    ("The floor has not been mopped", "Cleaning & Hygiene"),
    ("There is dust on the ceiling", "Cleaning & Hygiene"),
    ("There is dust near the ventilation window", "Cleaning & Hygiene"),
    ("The ventilation window is covered with dust", "Cleaning & Hygiene"),
    ("There are spider webs on the ceiling", "Cleaning & Hygiene"),
    ("There are spider webs in my room", "Cleaning & Hygiene"),
    ("The fan is covered with dust", "Cleaning & Hygiene"),
    ("There is too much dust on the fan", "Cleaning & Hygiene"),
    ("The balcony is not clean", "Cleaning & Hygiene"),
    ("The balcony has not been cleaned", "Cleaning & Hygiene"),
    ("Garbage has not been collected", "Cleaning & Hygiene"),
    ("Garbage is lying near my room", "Cleaning & Hygiene"),
    ("Waste has not been removed", "Cleaning & Hygiene"),
    ("There is algae in the sink", "Cleaning & Hygiene"),
    ("The sink is dirty and has algae", "Cleaning & Hygiene"),
    ("The sink needs cleaning", "Cleaning & Hygiene"),

    # Furniture & Room Fixtures
    ("The Bero lock is broken", "Furniture & Room Fixtures"),
    ("The Bero lock is not working", "Furniture & Room Fixtures"),
    ("The cupboard lock is damaged", "Furniture & Room Fixtures"),
    ("The Bero is making a screeching noise", "Furniture & Room Fixtures"),
    ("The Bero makes a strange noise when opened", "Furniture & Room Fixtures"),
    ("The mosquito net is torn", "Furniture & Room Fixtures"),
    ("The mosquito net is damaged", "Furniture & Room Fixtures"),
    ("The table is broken", "Furniture & Room Fixtures"),
    ("My study table is damaged", "Furniture & Room Fixtures"),
    ("The chair is broken", "Furniture & Room Fixtures"),
    ("The chair is shaky", "Furniture & Room Fixtures"),
    ("My chair is unstable", "Furniture & Room Fixtures"),
    ("The table has dents", "Furniture & Room Fixtures"),
    ("There are dents on my table", "Furniture & Room Fixtures"),
    ("The table has a splinter", "Furniture & Room Fixtures"),
    ("A part of the table has a splinter", "Furniture & Room Fixtures"),
    ("The study table is damaged", "Furniture & Room Fixtures"),

    # Building Maintenance
    ("There is a water stain on the wall", "Building Maintenance"),
    ("The wall has a water stain", "Building Maintenance"),
    ("There is a damaged area on the wall", "Building Maintenance"),
    ("The floor tile is cracked", "Building Maintenance"),
    ("One of the floor tiles is cracked", "Building Maintenance"),
    ("The floor tile is broken", "Building Maintenance"),
    ("A floor tile is damaged", "Building Maintenance"),
    ("The paint is peeling", "Building Maintenance"),
    ("The wall paint is coming off", "Building Maintenance"),
    ("The paint on the wall is damaged", "Building Maintenance"),
    ("The door frame is broken", "Building Maintenance"),
    ("The door frame is damaged", "Building Maintenance"),
    ("The bathroom wall is slightly damaged", "Building Maintenance"),
    ("Part of the bathroom wall is broken", "Building Maintenance"),
    ("There is damage to the bathroom wall", "Building Maintenance"),

    # Doors & Locks
    ("The room door cannot be locked", "Doors & Locks"),
    ("I cannot lock my room door", "Doors & Locks"),
    ("The door lock is not working", "Doors & Locks"),
    ("The room door lock is damaged", "Doors & Locks"),
    ("The window cannot be locked", "Doors & Locks"),
    ("I cannot lock the window", "Doors & Locks"),
    ("The window lock is not working", "Doors & Locks"),
    ("There is no latch on the door", "Doors & Locks"),
    ("The door does not have a proper latch", "Doors & Locks"),
    ("The door is misaligned", "Doors & Locks"),
    ("The room door is not aligned properly", "Doors & Locks"),
    ("The door does not close properly", "Doors & Locks"),

    # Internet / Network
    ("Hostel Wi-Fi is not available", "Internet / Network"),
    ("The hostel Wi-Fi is not working", "Internet / Network"),
    ("I cannot connect to the hostel Wi-Fi", "Internet / Network"),
    ("The Wi-Fi connection is not available in my room", "Internet / Network"),
    ("The hostel internet is not working", "Internet / Network"),
    ("Internet connection is unavailable", "Internet / Network"),
    ("The Wi-Fi keeps disconnecting", "Internet / Network"),
    ("The hostel Wi-Fi connection is unstable", "Internet / Network"),
    ("There is no Wi-Fi connection in my room", "Internet / Network"),
    ("The internet is very slow in my room", "Internet / Network"),

    # Pest Control
    ("There is a rat in my room", "Pest Control"),
    ("I saw a rat inside my room", "Pest Control"),
    ("A rat is entering my room", "Pest Control"),
    ("There are rats near my room", "Pest Control"),
    ("I noticed a rat in the hostel room", "Pest Control"),
    ("There is a rat problem in my room", "Pest Control"),
]

output_file = "ai/data/seed_dataset.csv"

with open(output_file, "w", newline="", encoding="utf-8") as file:
    writer = csv.writer(file)
    writer.writerow(["complaint", "category"])
    writer.writerows(data)

print(f"Created {output_file}")
print(f"Total complaints: {len(data)}")