import sys
import json

from ai_pipeline import analyze_complaint


def main():

    if len(sys.argv) < 2:

        print(json.dumps({
            "error": "No complaint provided"
        }))

        return


    complaint = sys.argv[1]


    try:

        result = analyze_complaint(
            complaint
        )

        print(
            json.dumps(
                result,
                ensure_ascii=False
            )
        )


    except Exception as e:

        print(
            json.dumps({
                "error": str(e)
            })
        )


if __name__ == "__main__":

    main()